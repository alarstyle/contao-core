<?php

/**
 * Carbid for Contao Open Source CMS
 *
 * Copyright (C) 2014-2016 Alexander Stulnikov
 *
 * @link       https://github.com/alarstyle/contao-carbid
 * @license    http://opensource.org/licenses/MIT
 */

namespace Carbid;

class Carbid
{

    public function initializeSystem()
    {

        // Add CSS and JS
        $GLOBALS['TL_JAVASCRIPT'][]     = 'system/modules/carbid/assets/js/jquery.min.js';
        $GLOBALS['TL_JAVASCRIPT'][]     = 'system/modules/carbid/assets/js/perfect-scrollbar.min.js';
        $GLOBALS['TL_JAVASCRIPT'][]     = 'system/modules/carbid/assets/js/carbid.js';
        $GLOBALS['TL_CSS'][]            = 'system/modules/carbid/assets/css/carbid.css';

        //
        if (\Config::get('ajaxEnabled') || 0)
        {
            $GLOBALS['TL_JAVASCRIPT'][]     = TL_ASSETS_URL . 'assets/tinymce4/tinymce.gzip.js';
            $GLOBALS['TL_JAVASCRIPT'][]     = TL_ASSETS_URL . "assets/ace/{$GLOBALS['TL_ASSETS']['ACE']}/ace.js'";
            $GLOBALS['TL_JAVASCRIPT'][]     = 'system/modules/carbid/assets/js/app.js';
        }

        // For Module Shortcut
        foreach ($GLOBALS['BE_MOD'] as &$arrGroup)
        {
            foreach ($arrGroup as $moduleName=>&$arrModule)
            {
                if ($arrModule['shortcut'])
                {
                    $arrModule['callback'] = 'Carbid\ModuleShortcut';
                    if ($moduleName == \Input::get('do'))
                    {
                        $arrSourceModule = &ModuleShortcut::getBackendModuleArr($arrModule['shortcut']['do']);
                        $arrModule = array_merge($arrSourceModule, $arrModule);
                    }
                    $GLOBALS['BE_MOD_SHORTCUT'][$moduleName] = &$arrModule;
                }
            }
        }



        /*$session = \Session::getInstance()->getData();
        var_dump($session['backend_modules']);
        var_dump('----');
        $session['backend_modules'] = array();
        \Session::getInstance()->set('backend_modules', array());
        $session = \Session::getInstance()->getData();
        var_dump($session['backend_modules']);*/
    }

    public function postAuthenticate()
    {
        // Force setting backend theme to default
        \Config::set('backendTheme', 'default');
    }

    public function getUserNavigation($arrModules, $blnShowAll)
    {
        // Add css classes
        foreach ($arrModules as $strGroupName => &$arrGroup)
        {
            if (!is_array($arrGroup['modules'])) continue;
            foreach ($arrGroup['modules'] as $strModuleName => &$arrModule)
            {
                if ( empty($GLOBALS['BE_MOD'][$strGroupName][$strModuleName]['class']) ) continue;
                $arrModule['class'] .= ' ' . $GLOBALS['BE_MOD'][$strGroupName][$strModuleName]['class'];
            }
        }

        // Do not change navigation if no shortcut modules
        if (!is_array($GLOBALS['BE_MOD_SHORTCUT']))
        {
            return $arrModules;
        }

        foreach ($arrModules as &$arrGroup)
        {
            if (!is_array($arrGroup['modules'])) continue;
            foreach ($arrGroup['modules'] as $moduleName=>&$arrModule)
            {
                if (isset($GLOBALS['BE_MOD_SHORTCUT'][$moduleName]))
                {
                    $module  = $GLOBALS['BE_MOD_SHORTCUT'][$moduleName];
                    $arrVars = $module['shortcut'];

                    $arrVars['ref'] = TL_REFERER_ID;
                    $arrVars['rt']  = REQUEST_TOKEN;

                    if (!$module['redirect'])
                    {
                        $arrVars['do'] = $moduleName;
                        $arrVars['nb']  = true;
                    }

                    $arrModule['href'] = TL_SCRIPT . '?' . http_build_query($arrVars, '', '&amp;');
                }
            }
        }

        return $arrModules;
    }


    public function parseBackendTemplate($strContent, $strTemplate)
    {
        $this->user = \BackendUser::getInstance();

        header('X-Current-Location: ' . \Environment::get('indexFreeRequest'));

        $this->addNoJsMessage($strContent, $strTemplate);
        $this->addCssClasses($strContent, $strTemplate);
        $this->addHeaderTitle($strContent, $strTemplate);
        $this->replaceFilterButton($strContent, $strTemplate);
        $this->removeTrash($strContent, $strTemplate);
        $this->addJsVariables($strContent, $strTemplate);

        // Replace image with svg
        /*$strContent = preg_replace('/<img[^>]*edit[_]?\.gif.*?>/', '<svg class="edit"><use xlink:href="http://kodi.dev/system/modules/carbid/assets/images/icons.svg#edit"></use></svg>', $strContent);*/

        // Show "loading" on autoSubmit
        //$strContent = str_replace('Backend.autoSubmit', "AjaxRequest.displayBox(Contao.lang.loading + ' …');Backend.autoSubmit", $strContent);

        return $strContent;
    }

    private function addNoJsMessage(&$strContent, $strTemplate)
    {
        $strContent = preg_replace('/(<body.*?>)/', '$1 <noscript class="no_js"><div>' . $GLOBALS['TL_LANG']['MSC']['js_disabled'] . '</div></noscript>', $strContent);
    }

    private function addCssClasses(&$strContent, $strTemplate)
    {
        $strClasses = ' template-' . $strTemplate;
        $strClasses .= ' do-' . \Input::get('do');
        $strClasses .= ' table-' . \Input::get('table');
        $strClasses .= ' act-' . \Input::get('act');
        foreach ($this->user->groups as $groupId)
        {
            $strClasses .= ' group-' . $groupId;
        }
        if (\Config::get('debugMode')) {
            $strClasses .= ' debug-enabled';
        }
        if (\Config::get('showLoginLanguage')) {
            $strClasses .= ' login-language-show';
        }
        if (\Input::get('popup')) {
            $strClasses .= ' popup';
        }
        $strContent = preg_replace('/(<html.*?)>/', '$1 class="'. trim($strClasses) .'">', $strContent);
    }

    private function addHeaderTitle(&$strContent, $strTemplate)
    {
        if (\Config::get('websiteTitle'))
        {
            $strContent =  preg_replace('/(<div id="header"?[^>]*>\s*<h1>)([^<]*)(<\/h1>)/m', '$1 ' . \Config::get('websiteTitle') . ' <small>$2</small>$3', $strContent);
        }
    }

    private function replaceFilterButton(&$strContent, $strTemplate)
    {
        $strContent = preg_replace('/<input(.*?)type="image"(.*?)>/', '<button $1 $2 $3></button>', $strContent);
    }

    private function removeTrash(&$strContent, $strTemplate)
    {
        $strContent = str_replace('onmouseover="Theme.hoverDiv(this,1)"', '', $strContent);
        $strContent = str_replace('onmouseout="Theme.hoverDiv(this,0)"', '', $strContent);
        $strContent = str_replace('onmouseover="Theme.hoverDiv(this, 1)"', '', $strContent);
        $strContent = str_replace('onmouseout="Theme.hoverDiv(this, 0)"', '', $strContent);
        $strContent = str_replace('onmouseover="Theme.hoverRow(this,1)"', '', $strContent);
        $strContent = str_replace('onmouseout="Theme.hoverRow(this,0)"', '', $strContent);
        $strContent = str_replace('Backend.getScrollOffset()', '', $strContent);
    }

    private function addJsVariables(&$strContent, $strTemplate)
    {
        $jsScript =
            '<script>' .
                'var userName = "' . $this->user->username . '";' .
            '</script>';
        $strContent = str_replace('</head>', $jsScript . "</head>", $strContent);
    }

}