<?php

namespace Grow;


class Route
{

    protected static $arr = null;


    public static function get()
    {
        if (static::$arr !== null) {
            return static::$arr;
        }

        $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $urlPath = ltrim($urlPath, '/');
        $urlPath = rtrim($urlPath, '/');

        return static::$arr = explode('/', $urlPath);
    }

}