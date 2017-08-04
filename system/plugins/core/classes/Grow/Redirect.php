<?php

namespace Grow;

class Redirect
{

    /**
     * Redirect to another page
     *
     * @param string  $url    The target URL
     * @param integer $status The HTTP status code (defaults to 303)
     */
    public static function toUrl($url, $status = 303)
    {
        if (headers_sent()) {
            exit;
        }
    }


    /**
     * Redirect to another route
     */
    public static function toRoute($route)
    {

    }



}