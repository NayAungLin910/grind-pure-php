<?php

namespace Src\Middlewares\User;

use Src\Middlewares\Middleware;
use Src\Router;

class UserAuth extends Middleware
{
    /**
     * Run security check on the client calling user
     */
    public function runSecurityCheck(): void
    {
       $router = new Router();

       session_start();

        if(!isset($_SESSION['auth'])) { // if not logged in
            $router->redirectUsingRouteName("show-login");
        }
    }
}
