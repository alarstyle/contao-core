<?php

namespace Gambling\Models;

use Contao\Model;

class BettingCategoryModel extends Model
{

    protected static $strTable = 'tl_casino_category';


    public static function findByAlias($alias)
    {
        $t = static::$strTable;

        $arrColumns = ["$t.alias=?"];
        $arrValues = [$alias];

        return static::findOneBy($arrColumns, $arrValues);
    }


    protected static function find(array $arrOptions)
    {
        $arrOptions['column'][] = 'is_betting = 1';

        return parent::find($arrOptions);
    }

}