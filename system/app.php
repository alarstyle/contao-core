<?php

use Contao\Environment;
use Contao\Input;
use Grow\Application;



/**
 * Check the request token upon POST requests
 */
if ($_POST && !\Contao\RequestToken::validate(Input::post('REQUEST_TOKEN'))) {
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


$app = Application::getInstance();

$app->run();
