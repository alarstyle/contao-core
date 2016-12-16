<?php

namespace Gambling;

use Contao\Config;
use Contao\Controller;
use Contao\Environment;
use Contao\Input;
use Contao\Session;
use Contao\System;
use Gambling\Models\CountryModel;
use Grow\Route;

class BackendHook
{

    public function parseBackendTemplate($strContent, $strTemplate)
    {
        if ($strTemplate === 'be_login') {
            $strContent = str_replace('</head>', '<link rel="stylesheet" href="system/plugins/gambling/assets/css/login.css"></head>', $strContent);
        }

        return $strContent;
    }

}