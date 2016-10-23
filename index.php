<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

// Set the script name
define('TL_SCRIPT', 'index.php');

// Initialize the system
define('TL_MODE', 'FE');
require_once __DIR__ . '/system/initialize.php';


// Run the controller
$controller = new \Contao\Controllers\FrontendIndex;
$controller->run();
