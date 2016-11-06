<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

// Set the script name
define('TL_SCRIPT', 'contao/switch.php');

// Initialize the system
define('TL_MODE', 'FE');
require dirname(__DIR__) . '/system/initialize.php';
require_once dirname(__DIR__) . '/system/temp.php';

// Run the controller
$controller = new BackendSwitch;
$controller->run();
