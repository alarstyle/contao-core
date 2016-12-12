<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 07.12.16
 * Time: 10:12
 */

namespace Gambling;


use Contao\Cache;
use Contao\Controller;
use Contao\Environment;
use Contao\Models\PageModel;
use Contao\System;
use Gambling\Models\CountryModel;
use Grow\Route;

class Gambling
{

    protected static $urls = [];

    protected static $pagesData = [];


    public static function getHomePage()
    {
        $currentCountryCode = Route::get()[0];

        return Environment::get('base') . $currentCountryCode . '/';
    }

    public static function getCountries()
    {
        if (Cache::has('countries')) {
            return Cache::get('countries');
        }

        $countriesRows = CountryModel::findAll();
        if (empty($countriesRows)) return null;

        $countries = [];
        $allCountries = System::getCountries();

        foreach ($countriesRows->fetchAll() as $country) {
            $countries[] = [
                'id' => $country['id'],
                'code' => $country['country'],
                'lang' => $country['language'],
                'title' => $allCountries[$country['country']],
                'link' => '#'
            ];
        }

        usort($countries, function ($a, $b) {
            if ($a['title'] === $b['title']) {
                return 0;
            }
            return ($a['title'] < $b['title']) ? -1 : 1;
        });

        Cache::set('countries', $countries);

        return $countries;
    }


    public static function getCurrentCountry()
    {
        if (Cache::has('currentCountry')) {
            return Cache::get('currentCountry');
        }

        $currentCountryCode = Route::get()[0];
        $countries = static::getCountries();
        $currentCountry = null;

        foreach ($countries as $country) {
            if ($country['code'] === $currentCountryCode) {
                $currentCountry = $country;
            }
        }

        Cache::set('currentCountry', $currentCountry);

        return $currentCountry;
    }


    public static function getFrontendUrl($pageId)
    {
        if (empty(static::$urls[$pageId])) {
            $currentCountry = static::getCurrentCountry();
            static::$urls[$pageId] = '/' . $currentCountry['code'] . '/' . Controller::generateFrontendUrl(PageModel::findByPk($pageId)->row());
        }

        return static::$urls[$pageId] ?: '';
    }


    public static function getPageTitle($pageId)
    {

    }

    public static function getPageData($pageId)
    {
        if (empty(static::$pagesData[$pageId])) {
            global $objPage;
            $pageRow = PageModel::findByPk($pageId)->row();
            $currentCountry = static::getCurrentCountry();
            $url = '/' . $currentCountry['code'] . '/' . Controller::generateFrontendUrl($pageRow);
            static::$pagesData[$pageId] = [
                'id' => $pageId,
                'url' => $url,
                'title' => 'some title',
                'current' => $pageId == $objPage->id
            ];
        }

        return static::$pagesData[$pageId] ?: [];
    }

}