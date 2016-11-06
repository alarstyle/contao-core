<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

// Set the script name
define('TL_SCRIPT', 'install.php');

// Initialize the system
define('TL_MODE', 'BE');
require_once dirname(__DIR__) . '/system/initialize.php';
require_once dirname(__DIR__) . '/system/temp.php';

// Show error messages
@ini_set('display_errors', 1);
error_reporting(\Contao\Config::get('errorReporting'));

// Run the controller
$controller = new \Contao\Controllers\BackendInstall;
$controller->run();
