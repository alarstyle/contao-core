<?php

namespace Gambling;

use Contao\BackendUser;
use Contao\Database;
use Contao\System;
use Gambling\Models\BettingCategoryModel;
use Gambling\Models\CasinoCategoryModel;
use Grow\ActionData;

class BackendHelpers
{
    protected static $database;


    protected static $countries = null;

    protected static $defaultCountryId = null;


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
                if ($result->fallback) {
                    static::$defaultCountryId = $result->id;
                }
                static::$countries[$result->id] = [
                    'id' => $result->id,
                    'code' => $result->country,
                    'name' => $countriesNames[$result->country] ?: $result->country,
                    'default' => $result->fallback
                ];
            }
        } else {
            static::$countries = [];
        }

        uasort(static::$countries, function ($a, $b) {
            if ($a['name'] === $b['name']) {
                return 0;
            }
            return ($a['name'] < $b['name']) ? -1 : 1;
        });
    }


    public static function getCountries()
    {
        static::loadCountries();

        return static::$countries;
    }


    public static function getDefaultCountry()
    {
        static::loadCountries();

        foreach (static::$countries as $country) {
            if ($country['default']) {
                return $country;
            }
        }

        return array_values(static::$countries)[0];
    }


    public static function getCountriesForOptions($idsArr = null)
    {
        static::loadCountries();

        $countriesNames = System::getCountriesWithFlags();
        $countries = [];

        foreach (static::$countries as $id=>$country) {
            if (!empty($idsArr) && !in_array($id, $idsArr)) continue;
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


    public static function getUserAvailableCountries()
    {
        static::loadCountries();

        $user = BackendUser::getInstance();

        if ($user->admin) {
            return array_keys(static::$countries);
        }

        return $user->countries;
    }


    public static function getUserAvailableCountriesForOptions()
    {
        $availableCountries = static::getUserAvailableCountries();

        return static::getCountriesForOptions($availableCountries);
    }


    public static function getCasinoCategoriesForOptions()
    {
        static::loadCountries();

        $categories = CasinoCategoryModel::findAll();

        if ($categories === null) return [];

        $categories = $categories->fetchAll();
        $options = [];
        $countryId = static::$defaultCountryId;

        foreach ($categories as $category) {
            $options[$category['id']] = deserialize($category['name'])[$countryId] ?: $category['alias'];;
        }

        return $options;
    }


    public static function getBettingCategoriesForOptions()
    {
        static::loadCountries();

        $categories = BettingCategoryModel::findAll();

        if ($categories === null) return [];

        $categories = $categories->fetchAll();
        $options = [];
        $countryId = static::$defaultCountryId;

        foreach ($categories as $category) {
            $options[$category['id']] = deserialize($category['name'])[$countryId] ?: $category['alias'];;
        }

        return $options;
    }

}