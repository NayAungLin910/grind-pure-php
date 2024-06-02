<?php

namespace Src\Middlewares\User;

use Src\Middlewares\Middleware;
use Src\Router;

class UserNotAuthMiddleware extends Middleware
{
    /**
     * Run security check on the current route
     */
    public function runSecurityCheck(): void
    {
        $router = new Router();

        session_start();

        if (isset($_SESSION['auth'])) { // if logged in
            $router->redirectUsingRouteName("welcome");
        }

        session_write_close();
    }
}
