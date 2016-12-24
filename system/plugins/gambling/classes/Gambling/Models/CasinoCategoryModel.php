<?php

namespace Gambling\Models;

use Contao\Model;

class CasinoCategoryModel extends Model
{

    protected static $strTable = 'tl_casino_category';


    public static function findByAlias($alias)
    {
        $t = static::$strTable;

        $arrColumns = ["$t.alias=?"];
        $arrValues = [$alias];

        return static::findOneBy($arrColumns, $arrValues);
    }

    public function test()
    {
        return 'test';
    }

}