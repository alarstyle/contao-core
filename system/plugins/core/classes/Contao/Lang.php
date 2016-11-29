<?php

namespace Contao;

class Lang
{

    public static function get($key)
    {
        list($key1, $key2) = explode('::', $key);
        return $GLOBALS['TL_LANG'][$key1][$key2];
    }

}