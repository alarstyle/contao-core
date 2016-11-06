<?php

use Contao\BackendUser;
use Contao\Environment;
use Contao\Input;



/**
 * Check the request token upon POST requests
 */
if (0 && $_POST && !\Contao\RequestToken::validate(Input::post('REQUEST_TOKEN'))) {
    // Force a JavaScript redirect upon Ajax requests (IE requires absolute link)
    if (Environment::get('isAjaxRequest')) {
        header('HTTP/1.1 204 No Content');
        header('X-Ajax-Location: ' . \Contao\Environment::get('base') . 'contao/');
    } else {
        header('HTTP/1.1 400 Bad Request');
        die_nicely('be_referer', 'Invalid request token. Please <a href="javascript:window.location.href=window.location.href">go back</a> and try again.');
    }

    exit;
}




$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$urlPath = ltrim($urlPath, '/');
$urlPath = rtrim($urlPath, '/');

$arrPath = explode('/', $urlPath);

if (count($arrPath) && $arrPath[0] === 'contao') {

    $backendUser = BackendUser::getInstance();
    if (isset($arrPath[1]) && $backendUser->authenticate()) {

        if (!empty($GLOBALS['RC_ROUTES']) && is_array($GLOBALS['RC_ROUTES']) && !empty($GLOBALS['RC_ROUTES'][$arrPath[1]])) {
            Input::setGet('do', $arrPath[1]);

            if (Environment::get('isAjaxRequest')) {
                \Contao\Config::set('debugMode', false);
                $class = $GLOBALS['RC_ROUTES'][$arrPath[1]];
                $controller = new $class();
                $controller->run();
                die;
            }
        }

        $controller = new \Contao\Controllers\BackendMain;
        $controller->run();

    } else {
        // Run the controller
        $controller = new \Contao\Controllers\BackendLogin;
        $controller->run();
    }

} else {
    // Run the controller
    $controller = new \Contao\Controllers\FrontendIndex;
    $controller->run();
}



//die;


