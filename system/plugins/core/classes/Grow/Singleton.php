<?php

namespace Grow;

abstract class Singleton
{

    /**
     * Object instance
     * @var Singleton
     */
    protected static $instance;


    protected function __construct() {}

    /**
     * Prevent cloning of an instance
     */
    final private function __clone() {}

    /**
     * Prevent unserializing of an instance
     */
    final private function __wakeup() {}


    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

}