<?php

namespace Gambling;

class Routes
{
    public static function casinoAffiliateLink($vars)
    {
        if (!$vars || !$vars['casinoAlias']) {
            static::notFound();
        }

        $casino = Gambling::getCasino($vars['casinoAlias']);

        if (!$casino || !$casino->casino_link) {
            static::notFound();
        }

        \Contao\Controller::redirect($casino->casino_link, 301);
    }

    public static function bettingAffiliateLink($vars)
    {
        if (!$vars || !$vars['casinoAlias']) {
            static::notFound();
        }

        $casino = Gambling::getCasino($vars['casinoAlias']);

        if (!$casino || !$casino->betting_link) {
            static::notFound();
        }

        \Contao\Controller::redirect($casino->betting_link, 301);
    }

    protected static function notFound()
    {
        header('HTTP/1.1 404 Not Found');
        exit('Page not found');
    }
}
