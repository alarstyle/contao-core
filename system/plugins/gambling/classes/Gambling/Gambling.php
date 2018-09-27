<?php

namespace Gambling;


use Contao\Cache;
use Contao\Controller;
use Contao\Database;
use Contao\Environment;
use Contao\Models\PageModel;
use Contao\System;
use Gambling\Models\CountryModel;
use Gambling\Models\PostModel;
use Grow\Route;

class Gambling
{
    const NEW_CASINO_CATEGORY_ID = 4;
    const NEW_BETTING_CATEGORY_ID = 9;

    protected static $pagesData = [];


    protected static $casinoCategories = null;
    protected static $bettingCategories = null;

    protected static $newCasinoCategory = null;
    protected static $newBettingCategory = null;

    protected static $translations = null;


    protected static $casinoAllOptions = null;


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
                'verificationCode' => $country['verificationCode'],
                'domain' => $country['domain'],
                'link' => $country['domain'] ? 'http://' . $country['domain'] : '/' . $country['alias'],
                'hidden' => $country['hidden']
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
            $currentCountryId = $currentCountry['id'];
            $url =
                (!empty($currentCountry['domain']) ?
                    'http://' . $currentCountry['domain'] :
                    '/' . $currentCountry['alias'])
                . '/' . Controller::generateFrontendUrl($pageRow);
            $navigationTitle = deserialize($pageRow['navigationTitle'])[$currentCountryId] ?: $pageRow['title'];
            $metaTitle = deserialize($pageRow['metaTitle'])[$currentCountryId];
            $metaDescription = deserialize($pageRow['metaDescription'])[$currentCountryId];
            static::$pagesData[$pageId] = [
                'id' => $pageId,
                'url' => rtrim($url, "/"),
                'current' => $pageId == $objPage->id,
                'navigationTitle' => $navigationTitle,
                'metaTitle' => $metaTitle ?: $navigationTitle,
                'metaDescription' => $metaDescription
            ];
        }

        return static::$pagesData[$pageId] ?: [];
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

        $currentCountry = static::getCurrentCountry();

        $connection = \Grow\Database::getConnection();
        $query = $connection->selectQuery()->table('tl_casino_category')
            ->fields(['*'])
            ->fields('tl_casino_category.id', 'id')
            ->join('tl_casino_category_data', 'data', 'left')
            ->on('tl_casino_category.id', 'data.pid')
            ->where('data.country', $currentCountry['id'])
            ->where('is_betting', '!=', 1)
            ->orderBy('data.sorting', 'desc');

        $categories = $query->execute()->asArray();

//        $categories = CasinoCategoryModel::findAll([
//            'order' => 'sorting DESC'
//        ]);

        if ($categories === null) return [];

//        $categories = $categories->fetchAll();

        $countryId = intval($currentCountry['id']);
        $casinosCategoryPage = static::getPageData(71);

        foreach ($categories as &$category) {
            $category->alias = deserialize($category->alias)[$countryId] ?: $category->id;
            $category->name = deserialize($category->name)[$countryId] ?: $category->id;
            $category->metaTitle = deserialize($category->metaTitle)[$countryId] ?: null;
            $category->metaDescription = deserialize($category->metaDescription)[$countryId] ?: null;
            $category->topTitle = deserialize($category->topTitle)[$countryId] ?: null;
            $category->topText = deserialize($category->topText)[$countryId] ?: null;
            $category->bottomTitle = deserialize($category->bottomTitle)[$countryId] ?: null;
            $category->bottomText = deserialize($category->bottomText)[$countryId] ?: null;
            $category->url = str_replace('{categoryAlias}', $category->alias, $casinosCategoryPage['url']);
            $category->current = $casinosCategoryPage['current'] && $category->alias === end(\Grow\Route::get());

            if ($category->id == static::NEW_CASINO_CATEGORY_ID) {
                static::$newCasinoCategory = $category;
            }
        }

        static::$casinoCategories = $categories;

        return static::$casinoCategories;
    }


    public static function getBettingCategories()
    {
        if (static::$bettingCategories !== null) {
            return static::$bettingCategories;
        }

        $currentCountry = static::getCurrentCountry();

        $connection = \Grow\Database::getConnection();
        $query = $connection->selectQuery()->table('tl_casino_category')
            ->fields(['*'])
            ->fields('tl_casino_category.id', 'id')
            ->join('tl_casino_category_data', 'data', 'left')
            ->on('tl_casino_category.id', 'data.pid')
            ->where('data.country', $currentCountry['id'])
            ->where('is_betting', 1)
            ->orderBy('data.sorting', 'desc');

        $categories = $query->execute()->asArray();

//        $categories = BettingCategoryModel::findAll();

        if ($categories === null) return [];

//        $categories = $categories->fetchAll();

        $countryId = intval($currentCountry['id']);
        $bettingCategoryPage = \Gambling\Gambling::getPageData(80);

        foreach ($categories as &$category) {
            $category->alias = deserialize($category->alias)[$countryId] ?: $category->id;
            $category->name = deserialize($category->name)[$countryId] ?: $category->id;
            $category->metaTitle = deserialize($category->metaTitle)[$countryId] ?: null;
            $category->metaDescription = deserialize($category->metaDescription)[$countryId] ?: null;
            $category->topTitle = deserialize($category->topTitle)[$countryId] ?: null;
            $category->topText = deserialize($category->topText)[$countryId] ?: null;
            $category->bottomTitle = deserialize($category->bottomTitle)[$countryId] ?: null;
            $category->bottomText = deserialize($category->bottomText)[$countryId] ?: null;
            $category->url = str_replace('{categoryAlias}', $category->alias, $bettingCategoryPage['url']);
            $category->current = $bettingCategoryPage['current'] && $category->alias === end(\Grow\Route::get());

            if ($category->id == static::NEW_BETTING_CATEGORY_ID) {
                static::$newBettingCategory = $category;
            }
        }

        static::$bettingCategories = $categories;

        return static::$bettingCategories;
    }


    public static function getCasinoCategory($alias)
    {
        $categories = static::getCasinoCategories();

        $casinoCategory = null;

        foreach($categories as $category) {
            if ($category->alias === $alias) {
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
            if ($category->alias === $alias) {
                $bettingCategory = $category;
                break;
            }
        }

        return $bettingCategory;
    }


    public static function getCasinos($categoryId, $limit, $offset, $order = null)
    {
        $currentCountry = static::getCurrentCountry();

        $connection = \Grow\Database::getConnection();
        $query = $connection->selectQuery()->table('tl_casino')
            ->fields(['*'])
            ->fields('tl_casino.id', 'id')
            ->join('tl_casino_data', 'data', 'left')
            ->on('tl_casino.id', 'data.pid')
            ->where('countries', 'like', '%"' . $currentCountry['id'] . '"%')
            ->where('data.country', $currentCountry['id'])
            ->where('is_casino', 1)
            ->where('data.published', 1)
            ->limit($limit);
        if (!$order) {
            $query
                ->orderBy('casino_sorting', 'desc')
                ->orderBy('tl_casino.id', 'desc');
        }
        else {
            $query->orderBy($order[0], $order[1]);
        }

        if ($categoryId) {
            $query->where('data.casino_categories', 'like', '%"' . $categoryId . '"%');
        }

        $casinos = $query->execute()->asArray();

        if (!count($casinos)) return [];

        $previewPage = static::getPageData(79);

        foreach ($casinos as $i=>$casino) {
            $casinos[$i] = static::prepareCasino($casino, $previewPage);
        }

        return $casinos;
    }


    public static function getBettings($categoryId, $limit, $offset, $order = null)
    {
        $currentCountry = static::getCurrentCountry();

        $connection = \Grow\Database::getConnection();
        $query = $connection->selectQuery()->table('tl_casino')
            ->fields(['*'])
            ->fields('tl_casino.id', 'id')
            ->join('tl_casino_data', 'data', 'left')
            ->on('tl_casino.id', 'data.pid')
            ->where('countries', 'like', '%"' . $currentCountry['id'] . '"%')
            ->where('data.country', $currentCountry['id'])
            ->where('is_betting', 1)
            ->where('data.published', 1)
            ->limit($limit);
        if (!$order) {
            $query
                ->orderBy('betting_sorting', 'desc')
                ->orderBy('tl_casino.id', 'desc');
        }
        else {
            $query->orderBy($order[0], $order[1]);
        }

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

        return $casino;
    }


    public static function getTranslation($variableName, $html = false)
    {
        $currentCountry = static::getCurrentCountry();
        $countryId = $currentCountry['id'];

        if (static::$translations === null) {
            $translationsFile = TL_ROOT . '/system/config/translations.php';
            if (!file_exists($translationsFile)) {
                static::$translations = [];
            }
            $allTranslations = include $translationsFile;
            if (!$allTranslations || !$allTranslations['frontend'] || !$allTranslations['frontend'][$countryId] || !is_array($allTranslations['frontend'][$countryId])) {
                static::$translations = [];
            }
            else {
                static::$translations = $allTranslations['frontend'][$countryId];
            }
        }

        $str = static::$translations[$variableName] ?: $variableName;

        return $html ? $str : htmlentities($str, ENT_NOQUOTES, 'UTF-8');
    }


    protected static function prepareCasino($casino, $previewPage)
    {
        $currentCountry = static::getCurrentCountry();
        $casino->url = str_replace('{casinoAlias}', $casino->alias, $previewPage['url']);

        if ($casino->cash_sign_up) {
            $casino->cash_sign_up_number = $casino->cash_sign_up;
            $casino->cash_sign_up = static::addCurrency($casino, $casino->cash_sign_up);
        }
        if ($casino->bet_bonus_deposit) {
            $casino->bet_bonus_deposit_number = $casino->bet_bonus_deposit;
            $casino->bet_bonus_deposit = static::addCurrency($casino, $casino->bet_bonus_deposit);
        }
        if ($casino->bet_bonus_sign_up) {
            $casino->bet_bonus_sign_up_number = $casino->bet_bonus_sign_up;
            $casino->bet_bonus_sign_up = static::addCurrency($casino, $casino->bet_bonus_sign_up);
        }
        if ($casino->withdrawal_min) {
            $casino->withdrawal_min = static::addCurrency($casino, $casino->withdrawal_min);
        }
        if ($casino->withdrawal_max) {
            $casino->withdrawal_max = static::addCurrency($casino, $casino->withdrawal_max);
        }
        if ($casino->deposit_min) {
            $casino->deposit_min = static::addCurrency($casino, $casino->deposit_min);
        }
        if ($casino->deposit_max) {
            $casino->deposit_max = static::addCurrency($casino, $casino->deposit_max);
        }

        if ($casino->is_casino && $casino->casino_link) {
            $casino->affiliate_link = $casino->casino_link;
            $casino->affiliate_same_window = $casino->casino_same_window;
        }
        elseif ($casino->betting_link) {
            $casino->affiliate_link = $casino->betting_link;
            $casino->affiliate_same_window = $casino->betting_same_window;
        }

        $casino->languages = deserialize($casino->languages) ?: [];
        $casino->deposit_bonuses = deserialize($casino->deposit_bonuses) ?: [];

//        $casino->wagering_casino = static::getCasinoOptions('wagering requirement', $casino->wagering_casino);
//        $casino->wagering_betting = static::getCasinoOptions('wagering requirement', $casino->wagering_betting);
        $casino->withdrawal_methods = static::getCasinoOptions('withdrawal methods', $casino->withdrawal_methods);
        $casino->deposit_methods = static::getCasinoOptions('deposit methods', $casino->deposit_methods);
        $casino->providers = static::getCasinoOptions('game providers', $casino->providers);
        $casino->licenses = static::getCasinoOptions('licenses', $casino->licenses);

        $depositCashTotal = 0;
        $casino->depositSpinsBonusTotal = 0;

        if ($casino->deposit_bonuses) {
            foreach ($casino->deposit_bonuses as $i=>$bonus) {
                if ($bonus['cash']) {
                    $casino->deposit_bonuses[$i]['cash'] = static::addCurrency($casino, $bonus['cash']);
                    $depositCashTotal += (int) $bonus['cash'];
                }
                if ($bonus['spins']) {
                    $casino->depositSpinsBonusTotal += (int) $bonus['spins'];
                }
            }
        }

        if ($depositCashTotal > 0) {
            $casino->depositCashBonusTotalNumber = $depositCashTotal;
            $casino->depositCashBonusTotal = static::addCurrency($casino, $depositCashTotal);
        }

        $casino->hasCasinoBonus = $casino->is_casino && ($casino->depositSpinsBonusTotal || $casino->depositCashBonusTotal || $casino->spins_sign_up || $casino->cash_sign_up);
        $casino->hasBettingBonus = $casino->is_betting && ($casino->bet_bonus_deposit || $casino->bet_bonus_sign_up);

        if (!$casino->is_casino) {
            $casino->casino_link = null;
        }

        if (!$casino->is_betting) {
            $casino->betting_link = null;
        }

        if ($casino->casino_link) {
            $casino->casinoLocalLink =
                (!empty($currentCountry['domain']) ?
                    'http://' . $currentCountry['domain'] :
                    '/' . $currentCountry['alias'])
                . '/go1/' . $casino->alias;
        }

        if ($casino->betting_link) {
            $casino->bettingLocalLink =
                (!empty($currentCountry['domain']) ?
                    'http://' . $currentCountry['domain'] :
                    '/' . $currentCountry['alias'])
                . '/go2/' . $casino->alias;
        }

        if ($casino->casino_categories) {
            $casino->casino_categories = deserialize($casino->casino_categories);
            if (is_array($casino->casino_categories) && in_array(static::NEW_CASINO_CATEGORY_ID, $casino->casino_categories)) {
                $casino->newCasino = true;
            }
        }

        if ($casino->betting_categories) {
            $casino->betting_categories = deserialize($casino->betting_categories);
            if (is_array($casino->betting_categories) && in_array(static::NEW_BETTING_CATEGORY_ID, $casino->betting_categories)) {
                $casino->newBetting = true;
            }
        }

        return $casino;
    }

    protected static function addCurrency($casino, $amount)
    {
        return $casino->currency_before !== '1'
            ? ($amount . ' ' . $casino->currency)
            : ($casino->currency . $amount);
    }

    protected static function getCasinoOptions($variableName, $valuesId)
    {
        $countryId = static::getCurrentCountry()['id'];
        $valuesId = deserialize($valuesId) ?: [];

        if (empty($valuesId) || !is_array($valuesId)) return [];

        if (static::$casinoAllOptions === null) {
            $optionsFile = TL_ROOT . '/system/config/options.php';
            if (!file_exists($optionsFile)) return [];
            $allOptions = include $optionsFile;
            static::$casinoAllOptions =  $allOptions ?: [];
        }

        if (!static::$casinoAllOptions || !static::$casinoAllOptions[$countryId] || !is_array(static::$casinoAllOptions[$countryId])) {
            return [];
        }

        $variableData = static::$casinoAllOptions[$countryId][$variableName];

        if (empty($variableData) || !is_array($variableData)) {
            return [];
        }

        $options = [];

        foreach ($variableData as $item) {
            if (!in_array($item['id'], $valuesId)) continue;
            $options[] = trim($item['label']);
        }

        usort($options, function ($a, $b) {
            if ($a === $b) {
                return 0;
            }
            return (strtolower($a) < strtolower($b)) ? -1 : 1;
        });

        return $options;
    }

}