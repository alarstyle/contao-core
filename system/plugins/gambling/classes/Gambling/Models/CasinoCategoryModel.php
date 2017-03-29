<?php

namespace Gambling\Models;

use Contao\Model;

class CasinoCategoryModel extends Model
{

    protected static $strTable = 'tl_casino_category';


    protected static function find(array $arrOptions)
    {
        $arrOptions['column'][] = 'NOT is_betting = 1';

        return parent::find($arrOptions);
    }

}