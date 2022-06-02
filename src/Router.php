<?php

namespace Maxters\Router;

use Maxters\Router\Exceptions\RouteNotFoundException;


/**
 * 
 * @author Wallace de Souza <wallacemaxters@gmail.com>
 */
class Router
{
    /**
     * @var Route[]
     */
    protected $routes = [];

    public function add(Route $route): static
    {
        $this->routes[] = $route;

        return $this;
    }

    public function createRoute(string $pattern, HttpVerbs $verb, callable $action): Route
    {
        if (!($action instanceof \Closure)) {
            $action = \Closure::fromCallable($action);
        }

        $route = new Route($pattern, $verb, $action);

        $this->add($route);

        return $route;
    }

    public function get(string $pattern, $action): Route
    {
        return $this->createRoute($pattern, HttpVerbs::GET, $action);
    }

    public function post(string $pattern, $action): Route
    {
        return $this->createRoute($pattern, HttpVerbs::POST, $action);
    }

    public function put(string $pattern, $action): Route
    {
        return $this->createRoute($pattern, HttpVerbs::PUT, $action);
    }

    public function delete(string $pattern, $action): Route
    {
        return $this->createRoute($pattern, HttpVerbs::DELETE, $action);
    }

    public function patch(string $pattern, $action): Route
    {
        return $this->createRoute($pattern, HttpVerbs::PATCH, $action);
    }

    public function trace(string $pattern, $action): Route
    {
        return $this->createRoute($pattern, HttpVerbs::TRACE, $action);
    }

    public function options(string $pattern, $action): Route
    {
        return $this->createRoute($pattern, HttpVerbs::OPTIONS, $action);
    }
    
    public function findRoute(string $pattern, HttpVerbs $verb): Route
    {
        foreach ($this->routes as $route) {
            if ($route->match($pattern) && $route->verb === $verb) {
                return $route;
            }
        }

        throw new RouteNotFoundException(
            "No route found with path \"{$pattern}\""
        );
    }

    public function execute(string $pattern, HttpVerbs $verb, array $params = [])
    {
        $route = $this->findRoute($pattern, $verb);

        return $route->execute($pattern, $params);
    }
}
