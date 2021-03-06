<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

// Set the script name
define('TL_SCRIPT', 'contao/main.php');

// Initialize the system
define('TL_MODE', 'BE');
require dirname(__DIR__) . '/system/initialize.php';
require dirname(__DIR__) . '/system/app.php';
