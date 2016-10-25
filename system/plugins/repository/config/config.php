<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   Repository
 * @author    Peter Koch, IBK Software AG
 * @license   See accompaning file LICENSE.txt
 * @copyright Peter Koch 2008-2010
 */


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['system']['repository_catalog'] = array
(
	'callback'   => 'RepositoryCatalog',
	'icon'       => 'system/plugins/repository/themes/default/images/catalog16.png',
	'stylesheet' => 'system/plugins/repository/themes/default/backend.css'
);

$GLOBALS['BE_MOD']['system']['repository_manager'] = array
(
	'callback'   => 'RepositoryManager',
	'icon'       => 'system/plugins/repository/themes/default/images/install16.png',
	'stylesheet' => 'system/plugins/repository/themes/default/backend.css'
);


/**
 * Register the templates
 */
\Contao\TemplateLoader::addFiles(array
(
	'repository_catlist' => 'system/plugins/repository/templates',
	'repository_catview' => 'system/plugins/repository/templates',
	'repository_mgredit' => 'system/plugins/repository/templates',
	'repository_mgrinst' => 'system/plugins/repository/templates',
	'repository_mgrlist' => 'system/plugins/repository/templates',
	'repository_mgruist' => 'system/plugins/repository/templates',
	'repository_mgrupdt' => 'system/plugins/repository/templates',
	'repository_mgrupgd' => 'system/plugins/repository/templates',
));
