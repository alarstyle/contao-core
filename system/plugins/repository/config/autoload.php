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
	'Repository'              => 'system/plugins/repository/classes/Repository.php',
	'RepositoryBackendModule' => 'system/plugins/repository/classes/RepositoryBackendModule.php',
	'RepositoryBackendTheme'  => 'system/plugins/repository/classes/RepositoryBackendTheme.php',
	'RepositoryCatalog'       => 'system/plugins/repository/classes/RepositoryCatalog.php',
	'RepositoryManager'       => 'system/plugins/repository/classes/RepositoryManager.php',
));


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
