<?php

namespace Src\Middlewares\Admin;

use Src\Middlewares\Middleware;
use Src\Router;

class AdminAuthMiddleware extends Middleware
{
    /**
     * Run security check on the current route
     */
    public function runSecurityCheck(): void
    {
        $router = new Router();

        session_start();

        if (!isset($_SESSION['auth'])) { // if not logged in
            $router->redirectUsingRouteName("show-login");
        }

        if ($_SESSION['auth']['role'] !== "admin") { // if logged in but not admin account
            $router->redirectUsingRouteName("welcome");
        }

        session_write_close();
    }
}
