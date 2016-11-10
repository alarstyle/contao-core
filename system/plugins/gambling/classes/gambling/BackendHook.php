<?php

namespace Gambling;

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