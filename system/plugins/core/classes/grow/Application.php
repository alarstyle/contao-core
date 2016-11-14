<?php

namespace Grow;

use Contao\BackendUser;
use Contao\Config;
use Contao\Environment;
use Contao\Input;

class Application
{

    protected static $instance;


    protected $arrRouteStack;


    public function __construct()
    {

    }


    public static function getInstance()
    {
        if (static::$instance === null)
        {
            static::$instance = new Application();
        }

        return static::$instance;
    }


    public function run()
    {
        $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $urlPath = ltrim($urlPath, '/');
        $urlPath = rtrim($urlPath, '/');

        $this->arrRouteStack = explode('/', $urlPath);

        if (count($this->arrRouteStack) && $this->arrRouteStack[0] === ltrim(Config::get('backendUri'), '/')) {
            $this->runBackend();
        } else {
            $this->runFrontend();
        }
    }


    protected function runBackend()
    {
        $arrPath = $this->arrRouteStack;

        $backendUser = BackendUser::getInstance();
        if (isset($arrPath[1]) && $backendUser->authenticate()) {

            $controllerClass = null;

            if (!empty($GLOBALS['RC_ROUTES']) && is_array($GLOBALS['RC_ROUTES']) && !empty($GLOBALS['RC_ROUTES'][$arrPath[1]])) {
                Input::setGet('do', $arrPath[1]);

                if (Environment::get('isAjaxRequest')) {
                    Config::set('debugMode', false);
                    $class = $GLOBALS['RC_ROUTES'][$arrPath[1]];
                    $controller = new $class();
                    $controller->run();
                    die;
                }

            }

            $controller = $controllerClass ? new $controllerClass() : $this->getBackendController();
            if (Environment::get('isAjaxRequest')) {
                $actionName = Input::post('ACTION');

                if ($controller->ajaxActions && !empty($controller->ajaxActions[$actionName])) {

                    header('Content-type: application/json');

                    $data = call_user_func([$controller, $controller->ajaxActions[$actionName]]);

                    exit(json_encode($data));

                }
                else {
                    echo 'no such action';
                    exit;
                }
                //$controller->
            }
            else {
                $controller->run();
            }

        } else {
            $controller = new \Contao\Controllers\BackendLogin();
            $controller->run();
        }
    }


    protected function runFrontend()
    {
        $controller = new \Contao\Controllers\FrontendIndex();
        $controller->run();
    }


    protected function getBackendController()
    {
        if ($GLOBALS['NAVIGATION'][$this->arrRouteStack[1]] && $GLOBALS['NAVIGATION'][$this->arrRouteStack[1]]['controller']) {
            $class = $GLOBALS['NAVIGATION'][$this->arrRouteStack[1]]['controller'];
            $config = $GLOBALS['NAVIGATION'][$this->arrRouteStack[1]]['config'] ?: null;
            $controller = new $class($config);
        } else {
            $controller = new \Contao\Controllers\BackendMain();
        }

        return $controller;
    }


    protected function getBackendControllerClass()
    {
        if ($GLOBALS['NAVIGATION'][$this->arrRouteStack[1]] && $GLOBALS['NAVIGATION'][$this->arrRouteStack[1]]['controller']) {
            $class = $GLOBALS['NAVIGATION'][$this->arrRouteStack[1]]['controller'];
        } else {
            $class = 'Contao\\Controllers\\BackendMain';
        }

        return $class;
    }

}