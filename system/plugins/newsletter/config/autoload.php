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
	'Contao\Newsletter'                => 'system/plugins/newsletter/classes/Newsletter.php',

	// Models
	'Contao\NewsletterChannelModel'    => 'system/plugins/newsletter/models/NewsletterChannelModel.php',
	'Contao\NewsletterModel'           => 'system/plugins/newsletter/models/NewsletterModel.php',
	'Contao\NewsletterRecipientsModel' => 'system/plugins/newsletter/models/NewsletterRecipientsModel.php',

	// Modules
	'Contao\ModuleNewsletterList'      => 'system/plugins/newsletter/modules/ModuleNewsletterList.php',
	'Contao\ModuleNewsletterReader'    => 'system/plugins/newsletter/modules/ModuleNewsletterReader.php',
	'Contao\ModuleSubscribe'           => 'system/plugins/newsletter/modules/ModuleSubscribe.php',
	'Contao\ModuleUnsubscribe'         => 'system/plugins/newsletter/modules/ModuleUnsubscribe.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_newsletter'        => 'system/plugins/newsletter/templates/modules',
	'mod_newsletter_list'   => 'system/plugins/newsletter/templates/modules',
	'mod_newsletter_reader' => 'system/plugins/newsletter/templates/modules',
	'nl_default'            => 'system/plugins/newsletter/templates/newsletter',
));
