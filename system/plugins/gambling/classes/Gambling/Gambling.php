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
use Gambling\Models\CasinoCategoryModel;
use Gambling\Models\CountryModel;
use Gambling\Models\PostModel;
use Grow\Route;

class Gambling
{

    protected static $pagesData = [];


    protected static $casinoCategories = null;


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
                'link' => '/' . $country['country'] . '/'
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


    public static function getTranslation($arr, $lang)
    {
        return '11';
    }


    public static function getNews($limit, $offset = 0, $options = null)
    {
        $currentCountry = static::getCurrentCountry();
        $postsPage = static::getPageData(78);

        $news = \Gambling\Models\PostModel::findNewsByCountryId($currentCountry['id'], $limit, $offset, $options);

        if ($news === null) return [];

        $news = $news->fetchAll();

        foreach ($news as $i=>&$newsItem) {
            $newsItem['url'] = str_replace('{id}', $newsItem['alias'], $postsPage['url']);
        }

        return $news;
    }


    public static function getPromotions($limit, $offset = 0, $options = null)
    {
        $currentCountry = static::getCurrentCountry();
        $postsPage = static::getPageData(78);

        $promotions = \Gambling\Models\PostModel::findPromotionsByCountryId($currentCountry['id'], $limit, $offset, $options);

        if ($promotions === null) return [];

        $promotions = $promotions->fetchAll();

        foreach ($promotions as $i=>&$promotion) {
            $promotion['url'] = str_replace('{id}', $promotion['alias'], $postsPage['url']);
        }

        return $promotions;
    }


    public static function getArticle($alias)
    {
        $article = PostModel::findByAlias($alias);

        return $article;
    }


    public static function getCasinoCategories()
    {
        if (static::$casinoCategories !== null) {
            return static::$casinoCategories;
        }

        $categories = CasinoCategoryModel::findAll();

        if ($categories === null) return [];

        $categories = $categories->fetchAll();
        $currentCountry = static::getCurrentCountry();
        $countryId = intval($currentCountry['id']);
        $casinosPage = \Gambling\Gambling::getPageData(71);

        foreach ($categories as &$category) {
            $category['name'] = deserialize($category['name'])[$countryId] ?: $category['alias'];
            $category['url'] = $casinosPage['url'] . '#' . '/';
        }

        static::$casinoCategories = $categories;

        return static::$casinoCategories;
    }

}