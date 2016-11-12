<?php

namespace Contao;

class Router
{

    private static $arrRouteStack = [];


    public static function get($uri, $callback)
    {

    }


    public static function post($uri, $callback)
    {

    }


    public static function any($uri, $callback)
    {

    }


    public static function match()
    {
        $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $urlPath = ltrim($urlPath, '/');
        $urlPath = rtrim($urlPath, '/');

        static::$arrRouteStack = explode('/', $urlPath);
    }


    public static function matchWithCurrent()
    {

    }

}