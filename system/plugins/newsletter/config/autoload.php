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
	'mod_newsletter'        => 'system/plugins/newsletter/templates/modules',
	'mod_newsletter_list'   => 'system/plugins/newsletter/templates/modules',
	'mod_newsletter_reader' => 'system/plugins/newsletter/templates/modules',
	'nl_default'            => 'system/plugins/newsletter/templates/newsletter',
));
