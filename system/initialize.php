<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Store the microtime
 */
define('TL_START', microtime(true));


/**
 * Define the root path to the Contao installation
 */
define('TL_ROOT', dirname(__DIR__));

if (!defined('TL_MODE')) {
    define('TL_MODE', 'temp');
}


/**
 * Define the login status constants in the back end (see #4099, #5279)
 */
if (TL_MODE == 'BE') {
    define('BE_USER_LOGGED_IN', false);
    define('FE_USER_LOGGED_IN', false);
}

define('TL_REFERER_ID', substr(md5(TL_START), 0, 8));


/**
 * Define the TL_SCRIPT constant (backwards compatibility)
 */
if (!defined('TL_SCRIPT')) {
    define('TL_SCRIPT', null);
}


/**
 * Include the helpers
 */
require TL_ROOT . '/system/helper/functions.php';
require TL_ROOT . '/system/config/constants.php';
require TL_ROOT . '/system/helper/interface.php';
require TL_ROOT . '/system/helper/exception.php';


/**
 * Try to disable the PHPSESSID
 */
@ini_set('session.use_trans_sid', 0);
@ini_set('session.cookie_httponly', true);


/**
 * Set the error and exception handler
 */
set_error_handler('__error');
set_exception_handler('__exception');


/**
 * Log PHP errors
 */
@ini_set('error_log', TL_ROOT . '/system/logs/error.log');


/**
 * Include some classes required for further processing
 */
require TL_ROOT . '/system/classes/Contao/Config.php';

require TL_ROOT . '/system/classes/Contao/TemplateLoader.php';

require TL_ROOT . '/system/classes/Contao/PluginLoader.php';

\Contao\Config::preload(); // see #5872


/**
 * Adjust the error handling
 */
@ini_set('display_errors', (Contao\Config::get('displayErrors') ? 1 : 0));
error_reporting((Contao\Config::get('displayErrors') || Contao\Config::get('logErrors')) ? Contao\Config::get('errorReporting') : 0);
set_error_handler('__error', Contao\Config::get('errorReporting'));


/**
 * Include the Composer autoloader
 */
require_once TL_ROOT . '/vendor/autoload.php';


/**
 * Try to load the modules
 */
try {
    $loader = new \Composer\Autoload\ClassLoader();

    $loader->addPsr4('', TL_ROOT . '/system/classes');
    foreach (\Contao\PluginLoader::getActive() as $module) {
        $loader->addPsr4('', TL_ROOT . '/system/plugins/' . $module . '/classes');
    }

    // activate the autoloader
    $loader->register();

    // to enable searching the include path (eg. for PEAR packages)
    $loader->setUseIncludePath(true);
} catch (UnresolvableDependenciesException $e) {
    die($e->getMessage()); // see #6343
}


/**
 * Override some SwiftMailer defaults
 */
Swift::init(function () {
    $preferences = Swift_Preferences::getInstance();

    if (!Contao\Config::get('useFTP')) {
        $preferences->setTempDir(TL_ROOT . '/system/tmp')->setCacheType('disk');
    }

    $preferences->setCharset(Contao\Config::get('characterSet'));
});


/**
 * Define the relative path to the installation (see #5339)
 */
if (file_exists(TL_ROOT . '/system/config/pathconfig.php') && TL_SCRIPT != 'install.php') {
    define('TL_PATH', include TL_ROOT . '/system/config/pathconfig.php');
} elseif (TL_MODE == 'BE') {
    define('TL_PATH', preg_replace('/\/contao\/[a-z]+\.php$/i', '', \Contao\Environment::get('scriptName')));
} else {
    define('TL_PATH', null); // cannot be reliably determined
}


/**
 * Start the session
 */
@session_set_cookie_params(0, (TL_PATH ?: '/')); // see #5339
@session_start();


/**
 * Get the Config instance
 */
$objConfig = Contao\Config::getInstance();


/**
 * Set the website path (backwards compatibility)
 */
\Contao\Config::set('websitePath', TL_PATH);


/**
 * Initialize the Input and RequestToken class
 */
\Contao\Input::initialize();
\Contao\RequestToken::initialize();


/**
 * Set the default language
 */
if (!isset($_SESSION['TL_LANGUAGE'])) {
    // Check the user languages
    $langs = \Contao\Environment::get('httpAcceptLanguage');
    array_push($langs, 'en'); // see #6533

    foreach ($langs as $lang) {
        if (is_dir(TL_ROOT . '/system/plugins/core/languages/' . str_replace('-', '_', $lang))) {
            $_SESSION['TL_LANGUAGE'] = $lang;
            break;
        }
    }

    unset($langs, $lang);
}

$GLOBALS['TL_LANGUAGE'] = $_SESSION['TL_LANGUAGE'];


/**
 * Show the "incomplete installation" message
 */
if (!$objConfig->isComplete() && TL_SCRIPT != 'install.php') {
    die_nicely('be_incomplete', 'The installation has not been completed. Open the Contao install tool to continue.');
}


/**
 * Always show error messages if logged into the install tool (see #5001)
 */
if (\Contao\Input::cookie('TL_INSTALL_AUTH') && !empty($_SESSION['TL_INSTALL_AUTH']) && \Contao\Input::cookie('TL_INSTALL_AUTH') == $_SESSION['TL_INSTALL_AUTH'] && $_SESSION['TL_INSTALL_EXPIRE'] > time()) {
    Contao\Config::set('displayErrors', 1);
}


/**
 * Set the timezone
 */
@ini_set('date.timezone', Contao\Config::get('timeZone'));
@date_default_timezone_set(Contao\Config::get('timeZone'));


/**
 * Set the mbstring encoding
 */
if (USE_MBSTRING && function_exists('mb_regex_encoding')) {
    mb_regex_encoding(Contao\Config::get('characterSet'));
}


/**
 * HOOK: add custom logic (see #5665)
 */
if (isset($GLOBALS['TL_HOOKS']['initializeSystem']) && is_array($GLOBALS['TL_HOOKS']['initializeSystem'])) {
    foreach ($GLOBALS['TL_HOOKS']['initializeSystem'] as $callback) {
        \Contao\System::importStatic($callback[0])->{$callback[1]}();
    }
}


/**
 * Include the custom initialization file
 */
if (file_exists(TL_ROOT . '/system/config/initconfig.php')) {
    include TL_ROOT . '/system/config/initconfig.php';
}

