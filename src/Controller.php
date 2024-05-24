<?php

namespace Src;

use Exception;

class Controller
{

    /**
     * Render the given view
     */
    public function render(string $view, array $data = []): void
    {
        if (!empty($data)) {
            extract($data);
        }

        include "routes.php";

        include "Views/$view.php";
    }

    /**
     * Redirects using route name
     */
    public function redirectUsingRouteName(string $routeName): void
    {
        if ($routeName == "") {
            throw new Exception("Please enter a route name");
        }

        include "routes.php";

        foreach ($router->routes as $url => $mappedInfos) {

            foreach ($router->routes[$url] as $method => $value) {

                $routeNameExists = isset($router->routes[$url][$method]["name"]); // the route name parameter is defined

                $routeNameSame = $router->routes[$url][$method]["name"] == $routeName; // check if the current route name is the same with the route being searched 

                if ($routeNameExists && $routeNameSame) {
                    header("Location: $url");
                    die();
                }
            }
        }

        throw new Exception("$routeName is not a declared route name.");
    }
}
