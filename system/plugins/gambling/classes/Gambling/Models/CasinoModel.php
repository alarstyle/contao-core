<?php

namespace Gambling\Models;

use Contao\Model;

class CasinoModel extends Model
{

    protected static $strTable = 'tl_casino';


    public static function findCasinos($countryId, $categoryId, $limit, $offset = 0, $customOptions = null)
    {
        $t = static::$strTable;
        $database = \Contao\Database::getInstance();

        $statement = $database->prepare("SELECT * FROM $t LEFT JOIN tl_casino_data data ON $t.id = data.pid " .
            "WHERE " .
                ($categoryId ? "$t.casino_categories LIKE '%\"$categoryId\"%' AND " : "" ) .
                "countries LIKE '%\"$countryId\"%' AND data.country = $countryId AND $t.isCasino = 1 AND data.published = 1 ORDER BY $t.id DESC");

        if ($limit) {
            $statement->limit($limit, $offset);
        }

        return $statement->execute();
    }

    public static function findBettings($countryId, $categoryId, $limit, $offset = 0, $customOptions = null)
    {
        $t = static::$strTable;
        $database = \Contao\Database::getInstance();

        $statement = $database->prepare("SELECT * FROM $t LEFT JOIN tl_casino_data data ON $t.id = data.pid " .
            "WHERE " .
                ($categoryId ? "$t.betting_categories LIKE '%\"$categoryId\"%' AND " : "") .
                "countries LIKE '%\"$countryId\"%' AND data.country = $countryId AND $t.isBetting = 1 AND data.published = 1 ORDER BY $t.id DESC");

        if ($limit) {
            $statement->limit($limit, $offset);
        }

        return $statement->execute();
    }


    public static function findByAlias($alias)
    {
        $t = static::$strTable;

        $arrColumns = ["$t.alias=?"];
        $arrValues = [$alias];

        return static::findOneBy($arrColumns, $arrValues);
    }

}