<?php

namespace Grow;


class ApplicationData
{

    private static $globalData = [];


    public static function addData($key, $data)
    {
        static::$globalData[$key] = $data;
    }

    public static function dataAsJson()
    {
        if (empty(static::$globalData)) {
            return '{}';
        }
        return json_encode(static::$globalData);
    }


    public static function forOutput()
    {
        return '<script>' .
            'window.APP_DATA=' . static::dataAsJson() .
            //'window.APP_METHODS=' . static::dataAsJson() .
        '</script>';
    }

}