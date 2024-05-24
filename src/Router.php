<?php

namespace Src;

use Src\Controller;

class Router
{
    public $routes = [];

    /**
     * Map get route and controller action
     */
    public function addGetRoute($route, $controller, $action): Router
    {
        $this->routes[$route]['GET'] = ['controller' => $controller, 'action' => $action];
        return $this;
    }

    /**
     * Map post route and controller action
     */
    public function addPostRoute($route, $controller, $action): Router
    {
        $this->routes[$route]['POST'] = ['controller' => $controller, 'action' => $action];
        return $this;
    }

    /**
     * Add middleware to guard the route
     */
    public function addMidleware($middleware): Router
    {
        $latestRoute = array_key_last($this->routes);
        $lastestMethodOfLatestRoute = array_key_last($this->routes[$latestRoute]);

        $this->routes[$latestRoute][$lastestMethodOfLatestRoute]['middleware'] = $middleware;

        return $this;
    }

    /**
     * Add route name
     */
    public function addRouteName($string): Router
    {
        $latestRoute = array_key_last($this->routes);
        $latestDefinedMethod = array_key_last($this->routes[$latestRoute]);

        $this->routes[$latestRoute][$latestDefinedMethod]["name"] = $string;

        return $this;
    }

    /**
     * Dispatch the current route and apply the mapped
     * method of the controller.
     */
    public function dispatch($uri): void
    {

        $routeExists = array_key_exists($uri, $this->routes); // the current route exists

        if ($routeExists) {
            $declaredRequestMethodExists = array_key_exists($_SERVER['REQUEST_METHOD'], $this->routes[$uri]); // the current request method of the route exists
        }

        if ($routeExists && $declaredRequestMethodExists) { // both the requested route and current method exists

            if (array_key_exists('middleware', $this->routes[$uri][$_SERVER['REQUEST_METHOD']])) { // if middleware exists

                $middleware = $this->routes[$uri][$_SERVER['REQUEST_METHOD']]['middleware'];
                $middlewareClass = new $middleware();

                $middlewareClass->runSecurityCheck(); // run secrity check for the route
            }

            $controller = $this->routes[$uri][$_SERVER['REQUEST_METHOD']]['controller']; // mapped controller of the current route
            $action = $this->routes[$uri][$_SERVER['REQUEST_METHOD']]['action']; // mapped controller of the current action
            $controller = new $controller();

            $controller->$action(); // call the method of the controller
        } else {
            $controller = new Controller();
            $controller->render('error/404');
        }
    }
}
