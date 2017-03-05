<?php

namespace Gambling;

use Contao\Controller;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Gambling\Models\CountryModel;
use Grow\Route;

class FrontendHook
{

    public function initializeSystem()
    {
        $currentCountry = Gambling::getCurrentCountry();

        $GLOBALS['TL_LANGUAGE'] = $currentCountry['lang'] ?: 'en';
    }


    public function generatePage($objPage, $objLayout, $page)
    {
        $currentCountry = Gambling::getCurrentCountry();
        $currentCountryId = $currentCountry['id'];

        $GLOBALS['TL_LANGUAGE'] = $objPage->language = $page->Template->language = $currentCountry['lang'] ?: 'en';

        $objPage->country = $page->Template->country = $currentCountry['code'];

        $navigationTitle = deserialize($objPage->navigationTitle)[$currentCountryId] ?: $objPage->title;
        $metaTitle = deserialize($objPage->metaTitle)[$currentCountryId] ?: $navigationTitle;
        $metaDescription = deserialize($objPage->metaDescription)[$currentCountryId];

        $objPage->title = $metaTitle;
        $objPage->description = $metaDescription ?: '';
    }


    public function beforeGetPageIdFromUrl()
    {
        $route = Route::get();

        if (empty($route) || empty($route[0])) {
            $this->redirectToCountry();
        }

        $countryAlias = $route[0];

        $countryObj = CountryModel::findByAlias($countryAlias);

        if (empty($countryObj)) {
            header('HTTP/1.1 404 Not Found');
            System::log('No active page, host "' . Environment::get('host') . '" (' . Environment::get('base') . Environment::get('request') . ')', __METHOD__, TL_ERROR);
            die('No page found');
        }

        $user_lang = $countryObj->language;

        System::setCookie('USER_COUNTRY', $countryAlias, time()+60*60*24*30);
        System::setCookie('USER_LANG', $user_lang, time()+60*60*24*30);

        $pageId = implode('/', array_slice($route, 1)) ?: 'index';

//        if (strpos($pageId, 'article/') === 0) {
//            $pageId = 'article/{id}';
//        }

        return null;
        //return $pageId;
    }


    public function getPageUri($uri) {
        $uri = explode('/', $uri);
        array_splice($uri, 1, 1);
        $uri = implode('/', $uri) ?: 'index';
        return $uri;
    }


    public function getPageById($pageId)
    {
        return null;
    }


    protected function redirectToCountry()
    {
        $countryAlias = Input::cookie('USER_COUNTRY');
        $countryObj = null;

        // Get country code by IP
        if (empty($countryAlias)) {
            $ip = Environment::get('ip');
            $geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip));
            $countryCode = strtolower($geo['geoplugin_countryCode']);
            $countryObj = CountryModel::findByCode($countryCode);
        }
        else {
            $countryObj = CountryModel::findByAlias($countryAlias);
        }

        if (empty($countryObj)) {
            // Get fallback country
            if (empty($countryObj)) {
                $countryObj = CountryModel::findFallback();
            }
        }

        // 404 if no country
        if (empty($countryObj)) {
            header('HTTP/1.1 404 Not Found');
            System::log('No fallback country', __METHOD__, TL_ERROR);
            die('No fallback country');
        }

        $countryAlias = $countryObj->alias;

        Controller::redirect($countryAlias . '/', 302);
    }

}