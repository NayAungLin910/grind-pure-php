<?php

namespace Src\Middlewares\Admin;

use Src\Middlewares\Middleware;
use Src\Router;
use Src\Services\MiddlewareService;

class AdminAuthMiddleware extends Middleware
{
    /**
     * Run security check on the current route
     */
    public function runSecurityCheck(): void
    {
        $router = new Router();

        if (!isset($_SESSION['auth']) && empty($_COOKIE['email'])) $router->redirectUsingRouteName("show-login");

        if (isset($_SESSION['auth']) && $_SESSION['auth']['role'] !== 'admin') $router->redirectUsingRouteName("welcome");

        if (!isset($_SESSION['auth']) && !empty($_COOKIE['email'])) { // if cookie exists

            $middlewareService = new MiddlewareService();
            $middlewareService->resetSectionFromExistingCookie();

            if ($_SESSION['auth']['role'] !== 'admin') $router->redirectUsingRouteName("welcome");
        }
    }
}
