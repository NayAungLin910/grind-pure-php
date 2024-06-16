<?php

namespace Src\Middlewares;

use Src\Middlewares\Middleware;
use Src\Models\User;
use Src\Router;
use Src\Services\AuthService;
use Src\Services\CookieService;
use Src\Services\MiddlewareService;

class AuthMiddleware extends Middleware
{
    /**
     * Run security check on the current route
     */
    public function runSecurityCheck(): void
    {
        $router = new Router();
        
        if (!isset($_SESSION['auth']) && empty($_COOKIE['email'])) $router->redirectUsingRouteName("show-login");
        
        if (!isset($_SESSION['auth']) && !empty($_COOKIE['email'])) { // if cookie exists
            
            $middlewareService = new MiddlewareService();
            $middlewareService->resetSectionFromExistingCookie();
        }
    }
}
