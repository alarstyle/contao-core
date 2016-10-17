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
