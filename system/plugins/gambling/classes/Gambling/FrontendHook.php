<?php

namespace Gambling;

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

        $GLOBALS['TL_LANGUAGE'] = $objPage->language = $page->Template->language = $currentCountry['lang'] ?: 'en';

        $objPage->country = $page->Template->country = $currentCountry['code'];
    }

}