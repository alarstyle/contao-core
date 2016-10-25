<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], 1, array
(
	'news' => array
	(
		'tables'      => array('tl_news_archive', 'tl_news', 'tl_news_feed', 'tl_content'),
		'icon'        => 'system/plugins/news/assets/icon.gif',
		'table'       => array('TableWizard', 'importTable'),
		'list'        => array('ListWizard', 'importList')
	)
));


/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD'], 2, array
(
	'news' => array
	(
		'newslist'    => 'Contao\\Modules\\ModuleNewsList',
		'newsreader'  => 'Contao\\Modules\\ModuleNewsReader',
		'newsarchive' => 'Contao\\Modules\\ModuleNewsArchive',
		'newsmenu'    => 'Contao\\Modules\\ModuleNewsMenu'
	)
));


/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['daily'][] = array('Contao\News', 'generateFeeds');


/**
 * Register hook to add news items to the indexer
 */
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] = array('Contao\News', 'purgeOldFeeds');
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('Contao\News', 'getSearchablePages');
$GLOBALS['TL_HOOKS']['generateXmlFiles'][] = array('Contao\News', 'generateFeeds');


/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'news';
$GLOBALS['TL_PERMISSIONS'][] = 'newp';
$GLOBALS['TL_PERMISSIONS'][] = 'newsfeeds';
$GLOBALS['TL_PERMISSIONS'][] = 'newsfeedp';


/**
 * Register the templates
 */
\Contao\TemplateLoader::addFiles(array
(
	'mod_newsarchive'   => 'system/plugins/news/templates/modules',
	'mod_newslist'      => 'system/plugins/news/templates/modules',
	'mod_newsmenu'      => 'system/plugins/news/templates/modules',
	'mod_newsmenu_day'  => 'system/plugins/news/templates/modules',
	'mod_newsmenu_year' => 'system/plugins/news/templates/modules',
	'mod_newsreader'    => 'system/plugins/news/templates/modules',
	'news_full'         => 'system/plugins/news/templates/news',
	'news_latest'       => 'system/plugins/news/templates/news',
	'news_short'        => 'system/plugins/news/templates/news',
	'news_simple'       => 'system/plugins/news/templates/news',
));
