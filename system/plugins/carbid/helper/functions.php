<?php

/**
 * Carbid for Contao Open Source CMS
 *
 * Copyright (C) 2014-2016 Alexander Stulnikov
 *
 * @link       https://github.com/alarstyle/contao-carbid
 * @license    http://opensource.org/licenses/MIT
 */


function lng($arr) {
    global $objPage;
    return $arr[$objPage->language] ?: '';
}


function url_by_id($pageId, $strParams=null, $strForceLang=null) {
    return \Frontend::generateFrontendUrl(\PageModel::findByPk($pageId)->row(), $strParams, $strForceLang);
}

/**
 * @deprecated
 */
function url_for_lang(array $arrObj, $strLang) {
    if (!empty($arrObj[$strLang])) {
        return url_by_id($arrObj[$strLang]);
    }
    return '';
}


/**
 * Inserts a new key/value before the key in the array.
 *
 * @param string $key The key to insert before.
 * @param array $array An array to insert in to.
 * @param string $new_key The key to insert.
 * @param mixed $new_value An value to insert.
 *
 * @return mixed The new array if the key exists, FALSE otherwise.
 */
function array_insert_before($key, array &$array, $new_key, $new_value) {
    if (array_key_exists($key, $array)) {
        $new = array();
        foreach ($array as $k => $value) {
            if ($k === $key) {
                $new[$new_key] = $new_value;
            }
            $new[$k] = $value;
        }
        return $new;
    }
    return FALSE;
}

/**
 * Inserts a new key/value after the key in the array.
 *
 * @param string $key The key to insert after.
 * @param array $array An array to insert in to.
 * @param string $new_key The key to insert.
 * @param mixed $new_value An value to insert.
 *
 * @return mixed The new array if the key exists, FALSE otherwise.
 */
function array_insert_after($key, array &$array, $new_key, $new_value)
{
    if (array_key_exists($key, $array))
    {
        $new = array();
        foreach ($array as $k => $value)
        {
            $new[$k] = $value;
            if ($k === $key)
            {
                $new[$new_key] = $new_value;
            }
        }
        return $new;
    }
    return false;
}


function render_template($strTemplate, $objData = null)
{
    $objTemplate = new \FrontendTemplate($strTemplate);
    $objTemplate->data = $objData;
    return $objTemplate->parse();
}


function file_by_uuid($strUuid, $boolFullPath = true, $boolUrlEncode = true)
{
    $objFile = \FilesModel::findByUuid($strUuid);
    if ($objFile !== null)
    {
        return ( $boolFullPath ? \Environment::get('base') : '' ) . ( $boolUrlEncode ? \System::urlEncode($objFile->path) : $objFile->path );
    }
    return '';
}


/**
 * Replace all emails in the string with mailto links
 *
 * @param string $str
 *
 * @return string
 */
function emails_to_links($str, $strText = null)
{
    $mail_pattern = "/([A-z0-9\._-]+\@[A-z0-9_-]+\.)([A-z0-9\_\-\.]{1,}[A-z])/";
    $str = preg_replace($mail_pattern, $strText ? '<a href="mailto:$1$2">'.$strText.'</a>' : '<a href="mailto:$1$2">$1$2</a>', $str);

    return $str;
}