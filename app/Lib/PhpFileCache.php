<?php
/*
 * 这个类暂时没用
 */

namespace App\Lib;

use Doctrine\Common\Cache\FileCache;

class PhpFileCache extends FileCache
{
    const EXTENSION = '.cache.php';

    /**
     * {@inheritdoc}
     */
    public function __construct($directory, $extension = self::EXTENSION, $umask = 0002)
    {
        parent::__construct($directory, $extension, $umask);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $value = $this->includeFileForId($id);

        if (! $value) {
            return false;
        }

        if ($value['lifetime'] !== 0 && $value['lifetime'] < time()) {
            return false;
        }

        return unserialize($value['data']);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        $value = $this->includeFileForId($id);

        if (! $value) {
            return false;
        }

        return $value['lifetime'] === 0 || $value['lifetime'] > time();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            $lifeTime = time() + $lifeTime;
        }

        $filename  = $this->getFilename($id);

        $value = array(
            'lifetime'  => $lifeTime,
            'data'      => serialize($data)
        );

        $value  = var_export($value, true);
        $code   = sprintf('<?php return %s;', $value);

        return $this->writeFile($filename, $code);
    }

    /**
     * @param string $id
     *
     * @return array|false
     */
    private function includeFileForId($id)
    {
        $fileName = $this->getFilename($id);

        // note: error suppression is still faster than `file_exists`, `is_file` and `is_readable`
        $value = @include $fileName;

        if (! isset($value['lifetime'])) {
            return false;
        }

        return $value;
    }
}
