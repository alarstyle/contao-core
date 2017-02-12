<?php

namespace Gambling\Models;

use Contao\Model;

class CountryModel extends Model
{

    protected static $strTable = 'tl_country';


    public static function findByCode($code)
    {
        $t = static::$strTable;
        $arrColumns = array("$t.country=?");

        return static::findOneBy($arrColumns, $code);
    }


    public static function findByAlias($alias)
    {
        $t = static::$strTable;
        $arrColumns = array("$t.alias=?");

        return static::findOneBy($arrColumns, $alias);
    }


    public static function findFallback()
    {
        $t = static::$strTable;
        $arrColumns = array("$t.fallback=1");

        return static::findOneBy($arrColumns, null);
    }

}