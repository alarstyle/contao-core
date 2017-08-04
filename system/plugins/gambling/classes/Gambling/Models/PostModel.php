<?php

namespace Gambling\Models;

use Contao\Model;

class PostModel extends Model
{

    protected static $strTable = 'tl_post';


    public static function findByAlias($alias)
    {
        $t = static::$strTable;

        $arrColumns = ["$t.alias=?", "$t.published=1"];
        $arrValues = [$alias];

        return static::findOneBy($arrColumns, $arrValues);
    }


    public static function findNewsByCountryId($countryId, $limit, $offset = 0, $customOptions = null)
    {
        $t = static::$strTable;

        $options = [
            'column' => array_merge(["$t.category = 5", "$t.country = $countryId", "$t.published = 1"]),
            'limit' => $limit,
            'offset' => $offset,
            'order' => 'date DESC',
            'return' => 'Contao\\Model\\Collection'
        ];

        if (is_string($customOptions)) {
            $options['column'][] = $customOptions;
        }

        return static::find($options);
    }


    public static function findPromotionsByCountryId($countryId, $limit, $offset = 0, $customOptions = null)
    {
        $t = static::$strTable;

        $options = [
            'column' => ["$t.category = 6", "$t.country = $countryId", "$t.published = 1"],
            'limit' => $limit,
            'offset' => $offset,
            'order' => 'date DESC',
            'return' => 'Contao\\Model\\Collection'
        ];

        if (is_string($customOptions)) {
            $options['column'][] = $customOptions;
        }

        return static::find($options);
    }

}