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
$GLOBALS['BE_MOD']['devtools'] = array
(
	'autoload' => array
	(
		'callback'   => 'Contao\\ModuleAutoload',
		'icon'       => 'system/plugins/devtools/assets/autoload.gif'
	),
	'extension' => array
	(
		'tables'     => array('tl_extension'),
		'create'     => array('Contao\\ModuleExtension', 'generate'),
		'icon'       => 'system/plugins/devtools/assets/extension.gif'
	),
	'labels' => array
	(
		'callback'   => 'Contao\\ModuleLabels',
		'icon'       => 'system/plugins/devtools/assets/labels.gif',
		'stylesheet' => 'system/plugins/devtools/assets/labels.css'
	)
);



/**
 * Register the templates
 */
\Contao\TemplateLoader::addFiles(array
(
	'dev_autoload'   => 'system/plugins/devtools/templates',
	'dev_beClass'    => 'system/plugins/devtools/templates',
	'dev_beTemplate' => 'system/plugins/devtools/templates',
	'dev_config'     => 'system/plugins/devtools/templates',
	'dev_dca'        => 'system/plugins/devtools/templates',
	'dev_default'    => 'system/plugins/devtools/templates',
	'dev_extension'  => 'system/plugins/devtools/templates',
	'dev_feClass'    => 'system/plugins/devtools/templates',
	'dev_feDca'      => 'system/plugins/devtools/templates',
	'dev_feTemplate' => 'system/plugins/devtools/templates',
	'dev_htaccess'   => 'system/plugins/devtools/templates',
	'dev_ini'        => 'system/plugins/devtools/templates',
	'dev_labels'     => 'system/plugins/devtools/templates',
	'dev_model'      => 'system/plugins/devtools/templates',
	'dev_modules'    => 'system/plugins/devtools/templates',
	'dev_table'      => 'system/plugins/devtools/templates',
));
