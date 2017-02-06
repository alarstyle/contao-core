<?php

namespace Gambling;


use Contao\Cache;
use Contao\Controller;
use Contao\Database;
use Contao\Environment;
use Contao\Models\PageModel;
use Contao\System;
use Gambling\Models\BettingCategoryModel;
use Gambling\Models\CasinoCategoryModel;
use Gambling\Models\CountryModel;
use Gambling\Models\PostModel;
use Grow\Route;

class Gambling
{

    protected static $pagesData = [];


    protected static $casinoCategories = null;
    protected static $bettingCategories = null;


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
        $casinosCategoryPage = static::getPageData(71);

        foreach ($categories as &$category) {
            $category['name'] = deserialize($category['name'])[$countryId] ?: $category['alias'];
            $category['url'] = str_replace('{categoryAlias}', $category['alias'], $casinosCategoryPage['url']);
            $category['current'] = $casinosCategoryPage['current'] && $category['alias'] === end(\Grow\Route::get());
        }

        static::$casinoCategories = $categories;

        return static::$casinoCategories;
    }


    public static function getBettingCategories()
    {
        if (static::$bettingCategories !== null) {
            return static::$bettingCategories;
        }

        $categories = BettingCategoryModel::findAll();

        if ($categories === null) return [];

        $categories = $categories->fetchAll();
        $currentCountry = static::getCurrentCountry();
        $countryId = intval($currentCountry['id']);
        $bettingCategoryPage = \Gambling\Gambling::getPageData(80);

        foreach ($categories as &$category) {
            $category['name'] = deserialize($category['name'])[$countryId] ?: $category['alias'];
            $category['url'] = str_replace('{categoryAlias}', $category['alias'], $bettingCategoryPage['url']);
            $category['current'] = $bettingCategoryPage['current'] && $category['alias'] === end(\Grow\Route::get());
        }

        static::$bettingCategories = $categories;

        return static::$bettingCategories;
    }


    public static function getCasinoCategory($alias)
    {
        $categories = static::getCasinoCategories();

        $casinoCategory = null;

        foreach($categories as $category) {
            if ($category['alias'] === $alias) {
                $casinoCategory = $category;
                break;
            }
        }

        return $casinoCategory;
    }


    public static function getBettingCategory($alias)
    {
        $categories = static::getBettingCategories();

        $bettingCategory = null;

        foreach($categories as $category) {
            if ($category['alias'] === $alias) {
                $bettingCategory = $category;
                break;
            }
        }

        return $bettingCategory;
    }


    public static function getCasinos($categoryId, $limit, $offset, $options = null)
    {
        $currentCountry = static::getCurrentCountry();
        $previewPage = static::getPageData(79);

        $casinos = \Gambling\Models\CasinoModel::findCasinos($currentCountry['id'], $categoryId, $limit, $offset, $options);

        if ($casinos === null || !$casinos->numRows) return [];

        $casinos = $casinos->fetchAllAssoc();

        foreach ($casinos as $i=>&$casino) {
            $casino['url'] = str_replace('{casinoAlias}', $casino['alias'], $previewPage['url']);
        }

        return $casinos;
    }


    public static function getBettings($categoryId, $limit, $offset, $options = null)
    {
        $currentCountry = static::getCurrentCountry();
        $previewPage = static::getPageData(79);

        $bettings = \Gambling\Models\CasinoModel::findBettings($currentCountry['id'], $categoryId, $limit, $offset, $options);

        if ($bettings === null || !$bettings->numRows) return [];

        $bettings = $bettings->fetchAllAssoc();

        foreach ($bettings as $i=>&$betting) {
            $betting['url'] = str_replace('{casinoAlias}', $betting['alias'], $previewPage['url']);
        }

        return $bettings;
    }


    public static function getCasino($alias)
    {
        $currentCountry = static::getCurrentCountry();
        $casino = \Gambling\Models\CasinoModel::findByAlias($alias);

        if ($casino === null) return null;

        $casino = $casino->row();

        $data = Database::getInstance()->prepare("SELECT * FROM `tl_casino_data` WHERE pid = ? AND country = ?")
            ->execute($casino['id'], $currentCountry['id']);

        if (!$data || !$data->published) return null;

        $prosArr = [];
        $consArr = [];
        foreach (explode("\n", $data->pros) as $pro) {
            if (!trim($pro)) continue;
            $prosArr[] = trim($pro);
        }
        foreach (explode("\n", $data->cons) as $con) {
            if (!trim($con)) continue;
            $consArr[] = trim($con);
        }
        $data->pros = $prosArr;
        $data->cons = $consArr;

        if (!$casino['isCasino']) {
            $data->casino_link = null;
        }

        if (!$casino['isBetting']) {
            $data->betting_link = null;
        }

        $casino['data'] = $data;

        return $casino;
    }

}