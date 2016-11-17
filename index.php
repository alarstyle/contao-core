<?php

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true) ?: $_POST;

// Set the script name
define('TL_SCRIPT', 'index.php');

// Initialize the system
define('TL_MODE', 'FE');
require __DIR__ . '/system/initialize.php';
require __DIR__ . '/system/app.php';
