<?php

namespace Grow;

class ActionData
{

    protected static $logArr = [];
    protected static $errorArr = [];
    protected static $dataArr = [];


    public static function log($log)
    {
        static::$logArr[] = $log;
    }


    public static function error($error)
    {
        if (is_array($error)) {
            static::$errorArr = array_merge(static::$errorArr, $error);
        }
        else {
            static::$errorArr[] = $error;
        }
    }


    public static function data($key, $data)
    {
        static::$dataArr[$key] = $data;
    }


    public static function output()
    {
        $data = [];

        if (count(static::$errorArr)) {
            $data['error'] = true;
            $data['errorData'] = static::$errorArr;
        } else {
            $data['success'] = true;
        }

        if (count(static::$logArr)) {
            $data['logs'] = static::$logArr;
        }

        $data['data'] = static::$dataArr;

        exit(json_encode($data));
    }

}