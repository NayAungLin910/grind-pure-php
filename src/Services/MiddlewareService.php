<?php

namespace Src\Services;

use Src\Models\User;
use Src\Router;

class MiddlewareService
{
    /**
     * Reset session from existing cookie
     */
    public function resetSectionFromExistingCookie()
    {
        $cookieService = new CookieService();
        $authServie = new AuthService();
        $router = new Router();

        $res = $cookieService->confirmRememberLoginCookie();

        if (!$res) $router->redirectUsingRouteName("show-login");

        require "../config/bootstrap.php";

        $user = $entityManager->getRepository(User::class)->findOneBy(["email" => $_COOKIE['email']]);

        if (!$user) {
            $authServie->clearCookies();
            $router->redirectUsingRouteName("show-login");
        }

        $_SESSION['auth'] = [ // reset the session
            "id" => $user->getId(),
            "name" => $user->getName(),
            "role" => $user->getRole(),
        ];
    }
}
