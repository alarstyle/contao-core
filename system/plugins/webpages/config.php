<?php

\Contao\TemplateLoader::addFiles([
    'be_shop_config'        => 'system/plugins/webpages/templates/backend',
    'be_pattern'            => 'system/plugins/webpages/templates/backend',
    'be_widget_hidden'      => 'system/plugins/webpages/templates/backend',
    'ce_pattern'            => 'system/plugins/webpages/templates/elements',
    'mod_pattern'           => 'system/plugins/webpages/templates/modules',
]);



// Pattern hooks
$GLOBALS['TL_HOOKS']['initializeSystem'][] = ['Grow\\Webpages\\Pattern', 'initializeSystemHook'];
$GLOBALS['TL_HOOKS']['loadLanguageFile'][] = ['Grow\\Webpages\\Pattern', 'loadLanguageFileHook'];
if (is_array($GLOBALS['TL_HOOKS']['loadDataContainer']))
{
    array_insert($GLOBALS['TL_HOOKS']['loadDataContainer'], 0, array('Grow\\Webpages\\Pattern', 'loadDataContainerHook'));
}
else
{
    $GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Grow\\Webpages\\Pattern', 'loadDataContainerHook');
}


// Pattern categories
$GLOBALS['TL_CTE']['patterns'] = array();
$GLOBALS['FE_MOD']['patterns'] = array();