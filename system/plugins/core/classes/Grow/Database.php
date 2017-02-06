<?php

namespace Grow;


use Contao\Config;

class Database
{

    protected static $instance;

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            $slice = new \PHPixie\Slice();
            $database = new \PHPixie\Database($slice->arrayData(array(
                'default' => array(
                    'driver' => 'pdo',
                    'connection' => 'mysql:host=' . Config::get('dbHost') . ';dbname=' . Config::get('dbDatabase'),
                    'user'       => Config::get('dbUser'),
                    'password'   => Config::get('dbPass')
                )
            )));
        }

        return static::$instance;
    }
}