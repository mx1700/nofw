<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 17/2/10
 * Time: 16:30
 */

namespace App\Core;

use DI\Container;
use Symfony\Component\Console\Application;

class ConsoleServerFactory
{
    public static function create(Container $c)
    {
        $commends = array_map(function($cmd) use ($c) {
            return $c->get($cmd);
        }, $c->get('commends'));
        $application = new Application();
        foreach ($commends as $commend) {
            $application->add($commend);
        }
        return $application;
    }
}