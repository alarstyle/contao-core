<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	//'Contao\News'              => 'system/plugins/news/classes/News.php',

	// Models
	'Contao\NewsArchiveModel'  => 'system/plugins/news/models/NewsArchiveModel.php',
	'Contao\NewsFeedModel'     => 'system/plugins/news/models/NewsFeedModel.php',
	'Contao\NewsModel'         => 'system/plugins/news/models/NewsModel.php',

	// Modules
	'Contao\ModuleNews'        => 'system/plugins/news/modules/ModuleNews.php',
	'Contao\ModuleNewsArchive' => 'system/plugins/news/modules/ModuleNewsArchive.php',
	'Contao\ModuleNewsList'    => 'system/plugins/news/modules/ModuleNewsList.php',
	'Contao\ModuleNewsMenu'    => 'system/plugins/news/modules/ModuleNewsMenu.php',
	'Contao\ModuleNewsReader'  => 'system/plugins/news/modules/ModuleNewsReader.php',
));


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
