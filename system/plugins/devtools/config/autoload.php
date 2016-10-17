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
