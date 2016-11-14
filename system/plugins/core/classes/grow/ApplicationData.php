<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 11.11.16
 * Time: 18:45
 */

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