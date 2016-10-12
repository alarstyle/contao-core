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
