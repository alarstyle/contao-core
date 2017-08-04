<?php

namespace Grow\Routing;

class Route
{
    const NOT_FOUND = 0;
    const FOUND = 1;
    const METHOD_NOT_ALLOWED = 2;

    public $status;

    public $handler;

    public $vars;

    public function __construct($status, $handler, $vars)
    {

    }


}