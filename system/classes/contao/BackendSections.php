<?php

namespace Contao;

class BackendSections
{

    protected static $sections = [];

//    public static function set(array $arrSections)
//    {
//        return static::$sections = $arrSections;
//    }

    public static function get($strSectionName)
    {
        return static::$sections[$strSectionName] ?: [];
    }

    public static function add($strSectionName, $callback)
    {
        static::$sections[$strSectionName][] = $callback;
    }

//    public static function remove($strSectionName)
//    {
//
//    }

}