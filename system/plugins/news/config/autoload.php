<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


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
