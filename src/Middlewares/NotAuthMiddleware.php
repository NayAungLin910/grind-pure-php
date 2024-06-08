<?php

namespace Src\Middlewares;

use Src\Middlewares\Middleware;
use Src\Router;

class NotAuthMiddleware extends Middleware
{
    /**
     * Run security check on the current route
     */
    public function runSecurityCheck(): void
    {
        $router = new Router();

        if (isset($_SESSION['auth'])) { // if logged in
            $router->redirectUsingRouteName("welcome");
        }
    }
}
