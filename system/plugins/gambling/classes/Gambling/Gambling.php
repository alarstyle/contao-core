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
                'flag' => $country['country'],
                'alias' => $country['alias'],
                'lang' => $country['language'],
                'title' => $allCountries[$country['country']],
                'link' => '/' . $country['alias'] . '/'
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

        $currentCountryAlias = Route::get()[0];
        $countries = static::getCountries();
        $currentCountry = null;

        foreach ($countries as $country) {
            if ($country['alias'] === $currentCountryAlias) {
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
            $url = '/' . $currentCountry['alias'] . '/' . Controller::generateFrontendUrl($pageRow);
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

        $connection = \Grow\Database::getConnection();
        $query = $connection->selectQuery()->table('tl_casino')
            ->join('tl_casino_data', 'data', 'left')
            ->on('tl_casino.id', 'data.pid')
            ->where('countries', 'like', '%"' . $currentCountry['id'] . '"%')
            ->where('data.country', $currentCountry['id'])
            ->where('is_casino', 1)
            ->where('data.published', 1)
            ->orderBy('tl_casino.id', 'desc');
        if ($categoryId) {
            $query->where('data.casino_categories', 'like', '%"' . $categoryId . '"%');
        }
        $casinos = $query->execute()->asArray();

        if (!count($casinos)) return [];

        $previewPage = static::getPageData(79);

//        $casinos = \Gambling\Models\CasinoModel::findCasinos($currentCountry['id'], $categoryId, $limit, $offset, $options);
//
//        if ($casinos === null || !$casinos->numRows) return [];
//
//        $casinos = $casinos->fetchAllAssoc();

        foreach ($casinos as $i=>$casino) {
            $casinos[$i] = static::prepareCasino($casino, $previewPage);
        }

        return $casinos;
    }


    public static function getBettings($categoryId, $limit, $offset, $options = null)
    {
        $currentCountry = static::getCurrentCountry();

        $connection = \Grow\Database::getConnection();
        $query = $connection->selectQuery()->table('tl_casino')
            ->join('tl_casino_data', 'data', 'left')
            ->on('tl_casino.id', 'data.pid')
            ->where('countries', 'like', '%"' . $currentCountry['id'] . '"%')
            ->where('data.country', $currentCountry['id'])
            ->where('is_betting', 1)
            ->where('data.published', 1)
            ->orderBy('tl_casino.id', 'desc');
        if ($categoryId) {
            $query->where('data.betting_categories', 'like', '%"' . $categoryId . '"%');
        }
        $bettings = $query->execute()->asArray();

        if (!count($bettings)) return [];

        $previewPage = static::getPageData(79);

        foreach ($bettings as $i=>$betting) {
            $bettings[$i] = static::prepareCasino($betting, $previewPage);
        }

        return $bettings;
    }


    public static function getCasino($alias)
    {
        $currentCountry = static::getCurrentCountry();

        $connection = \Grow\Database::getConnection();
        $casino = $connection->selectQuery()->table('tl_casino')
            ->join('tl_casino_data', 'data', 'left')
                ->on('tl_casino.id', 'data.pid')
            ->where('alias', $alias)
            ->where('data.country', $currentCountry['id'])
            ->limit(1)
            ->execute()->asArray();

        if (!count($casino) || !$casino[0]->published) return null;

        $casino = $casino[0];

        $previewPage = static::getPageData(79);
        $casino = static::prepareCasino($casino, $previewPage);

        $prosArr = [];
        $consArr = [];
        foreach (explode("\n", $casino->pros) as $pro) {
            if (!trim($pro)) continue;
            $prosArr[] = trim($pro);
        }
        foreach (explode("\n", $casino->cons) as $con) {
            if (!trim($con)) continue;
            $consArr[] = trim($con);
        }
        $casino->pros = $prosArr;
        $casino->cons = $consArr;

        if (!$casino->is_casino) {
            $casino->casino_link = null;
        }

        if (!$casino->is_betting) {
            $casino->betting_link = null;
        }

        return $casino;
    }


    protected static function prepareCasino($casino, $previewPage)
    {
        $casino->url = str_replace('{casinoAlias}', $casino->alias, $previewPage['url']);

        $cashTotal =
            ((int) $casino->cash_1_deposit ?: 0) +
            ((int) $casino->cash_2_deposit ?: 0) +
            ((int) $casino->cash_3_deposit ?: 0);
        $casino->depositSpinsBonusTotal =
            ((int) $casino->spins_1_deposit ?: 0) +
            ((int) $casino->spins_2_deposit ?: 0) +
            ((int) $casino->spins_2_deposit ?: 0);
        if ($cashTotal > 0) {
            $casino->depositCashBonusTotal = static::addCurrency($casino, $cashTotal);
        }
        if ($casino->cash_1_deposit) {
            $casino->cash_1_deposit = static::addCurrency($casino, $casino->cash_1_deposit);
        }
        if ($casino->cash_2_deposit) {
            $casino->cash_2_deposit = static::addCurrency($casino, $casino->cash_2_deposit);
        }
        if ($casino->cash_3_deposit) {
            $casino->cash_3_deposit = static::addCurrency($casino, $casino->cash_3_deposit);
        }
        if ($casino->cash_sign_up) {
            $casino->cash_sign_up = static::addCurrency($casino, $casino->cash_sign_up);
        }
        if ($casino->bet_bonus_deposit) {
            $casino->bet_bonus_deposit = static::addCurrency($casino, $casino->bet_bonus_deposit);
        }
        if ($casino->bet_bonus_sign_up) {
            $casino->bet_bonus_sign_up = static::addCurrency($casino, $casino->bet_bonus_sign_up);
        }

        $casino->hasCasinoBonus = $casino->depositSpinsBonusTotal || $casino->depositCashBonusTotal || $casino->spins_sign_up || $casino->cash_sign_up;
        $casino->hasBettingBonus = $casino->bet_bonus_deposit || $casino->bet_bonus_sign_up;

        return $casino;
    }

    protected static function addCurrency($casino, $amount)
    {
        return $casino->currency_before !== '1'
            ? ($amount . ' ' . $casino->currency)
            : ($casino->currency . $amount);
    }

}