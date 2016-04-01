<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 16/3/31
 * Time: 9:12
 */

namespace App\Lib;


use \Doctrine\Common\Cache\CacheProvider;
use \Doctrine\Common\Cache;

/**
 * 缓存类，用以缓存 DI 容器的配置
 * Class YacCache
 * @package App\Lib
 */
class YacCache extends CacheProvider
{
    private $yac;

    public function __construct(\Yac $yac)
    {
        $this->yac = $yac;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return mixed|false The cached data or FALSE, if no cache entry exists for the given id.
     */
    protected function doFetch($id)
    {
        return $this->yac->get($this->getHash($id));
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    protected function doContains($id)
    {
        return $this->yac->get($this->getHash($id)) !== false;
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param string $data The cache entry/data.
     * @param int $lifeTime The lifetime. If != 0, sets a specific lifetime for this
     *                           cache entry (0 => infinite lifeTime).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $this->yac->set($this->getHash($id), $data);
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    protected function doDelete($id)
    {
        $this->yac->delete($this->getHash($id));
    }

    /**
     * Flushes all cache entries.
     *
     * @return bool TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    protected function doFlush()
    {
        $this->yac->flush();
    }

    /**
     * Retrieves cached information from the data store.
     *
     * @since 2.2
     *
     * @return array|null An associative array with server's statistics if available, NULL otherwise.
     */
    protected function doGetStats()
    {
        $info = $this->yac->info();

        return array(
            Cache::STATS_HITS             => $info['hits'],
            Cache::STATS_MISSES           => $info['miss'],
            Cache::STATS_UPTIME           => 0,
            Cache::STATS_MEMORY_USAGE     => 0,
            Cache::STATS_MEMORY_AVAILABLE => 0,
        );
    }

    private function getHash($id)
    {
        $hash = md5($id);
        return $hash;
    }
}