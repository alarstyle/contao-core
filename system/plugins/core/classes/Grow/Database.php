<?php

namespace Grow;


use Contao\Config;

class Database
{

    protected static $instance;

    protected static $database;

    protected static $connection;

    public static function getInstance()
    {
        if (!isset(static::$instance)) {

        }

        return static::$instance;
    }

    public static function getDatabase()
    {
        if (!isset(static::$database)) {
            $slice = new \PHPixie\Slice();
            static::$database = new \PHPixie\Database($slice->arrayData(array(
                'default' => array(
                    'driver' => 'pdo',
                    'connection' => 'mysql:host=' . Config::get('dbHost') . ';dbname=' . Config::get('dbDatabase'),
                    'user'       => Config::get('dbUser'),
                    'password'   => Config::get('dbPass')
                )
            )));
        }

        return static::$database;
    }

    public static function getConnection()
    {
        if (!isset(static::$connection)) {
            if (!isset(static::$database)) {
                static::getDatabase();
            }
            static::$connection = static::$database->get('default');
        }

        return static::$connection;
    }
}