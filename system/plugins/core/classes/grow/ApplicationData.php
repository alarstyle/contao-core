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

    private static $data = [];

    public static function add()
    {

    }

    public static function getJson()
    {
        return json_encode(static::$data);
    }

}