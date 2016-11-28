<?php

namespace Gambling;

use Contao\Database;
use Contao\System;
use Grow\ActionData;

class BackendHelpers
{
    protected static $database;


    protected static $countries = null;


    protected static function loadCountries()
    {
        if (static::$countries !== null) return;

        if (empty(static::$database)) {
            static::$database = Database::getInstance();
        }

        $result = static::$database->prepare("SELECT * FROM tl_country")
            ->execute();

        if ($result->numRows) {
            $countriesNames = \Contao\System::getCountries();
            while ($result->next()) {
                static::$countries[$result->id] = [
                    'code' => $result->country,
                    'name' => $countriesNames[$result->country] ?: $result->country
                ];
            }
        } else {
            static::$countries = [];
        }
    }


    public static function getCountriesForOptions()
    {
        static::loadCountries();

        $countriesNames = System::getCountriesWithFlags();
        $countries = [];

        foreach (static::$countries as $id=>$country) {
            $countries[$id] = $countriesNames[$country['code']];
        }

        return $countries;
    }


    public static function getCountriesFlagsByIds($ids)
    {
        static::loadCountries();

        $countriesFlags = '';

        foreach ($ids as $id) {
            if (empty(static::$countries[$id])) continue;
            $countryCode = static::$countries[$id]['code'];
            $countryName = static::$countries[$id]['name'];
            $countriesFlags .= '<span class="flag flag-' . $countryCode . '" title="' . $countryName . '"></span>';
        }

        return $countriesFlags;
    }

}