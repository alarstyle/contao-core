<?php

namespace Grow\Routing;

use FastRoute\RouteCollector;
//use Psr\Http\Message\ServerRequestInterface;

use InvalidArgumentException;

class Router
{

    /**
     * @var \FastRoute\Dispatcher
     */
    protected $dispatcher;


    protected $routes = [];

    /**
     * Create new router
     */
    public function __construct()
    {

    }

    /**
     * Add route
     *
     * @param  string[] $methods Array of HTTP methods
     * @param  string   $pattern The route pattern
     * @param  callable $handler The route callable
     *
     * @throws InvalidArgumentException if the route pattern isn't a string
     */
    public function map($methods, $pattern, $handler)
    {
        $this->addRoute($methods, $pattern, $handler);
    }

    public function get($pattern, $handler)
    {
        $this->addRoute(['GET'], $pattern, $handler);
    }

    public function post($pattern, $handler)
    {
        $this->addRoute(['POST'], $pattern, $handler);
    }

    public function dispatch($httpMethod, $uri)
    {
        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        return $this->createDispatcher()->dispatch($httpMethod, $uri);
    }

    protected function addRoute($methods, $pattern, $handler) {
        if (!is_string($pattern)) {
            throw new InvalidArgumentException('Route pattern must be a string');
        }

        $this->routes[] = [
            'methods' => $methods,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    /**
     * @return \FastRoute\Dispatcher
     */
    protected function createDispatcher()
    {
        if ($this->dispatcher) {
            return $this->dispatcher;
        }

        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $route) {
                $pattern = '/' . ltrim($route['pattern'], '/');
                $pattern = rtrim($pattern, '/');
                $r->addRoute($route['methods'], $pattern, $route['handler']);
                $r->addRoute($route['methods'], $pattern . '/', $route['handler']);
            }
        });

        return $this->dispatcher;
    }
}