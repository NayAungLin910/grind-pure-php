<?php

namespace Src\Middlewares;

use Src\Middlewares\Middleware;
use Src\Models\User;
use Src\Router;
use Src\Services\CookieService;

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

            $cookieService = new CookieService();

            $res = $cookieService->confirmRememberLoginCookie();

            if (!$res) $router->redirectUsingRouteName("show-login");

            $user = User::selectAll()->where('email', $_COOKIE["email"])->getSingle();

            $_SESSION['auth'] = [ // reset the session
                "id" => $user->id,
                "name" => $user->name,
                "role" => $user->role,
            ];
        }
    }
}
