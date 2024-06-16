<?php

namespace Src\Services;

use Src\Models\User;

class CookieService
{
    /**
     * Check if the login remember cookie exist and is legitimate
     */
    public function confirmRememberLoginCookie(): bool
    {
        $authService = new AuthService();

        if (empty($_COOKIE["email"]) || empty($_COOKIE["rand_token"]) || empty($_COOKIE["rand_selector_token"])) { // if one of the cookies is empty \
            $authService->clearCookies();
            return false;
        }

        require "../config/bootstrap.php";

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $_COOKIE['email']
        ]);

        if (!$user) {
            $authService->clearCookies();
            return false;
        }

        $token = $authService->getTokenUsingEmail($user->getEmail());

        if (!$token) {
            $authService->clearCookies();
            return false;
        }

        $randTokenVerify = password_verify($_COOKIE["rand_token"], $token["password"]);
        $randSelectorTokenVerify = password_verify($_COOKIE["rand_selector_token"], $token["selector"]);

        if (!$randTokenVerify || !$randSelectorTokenVerify) { // if the token or the selector token is not legitimate
            $authService->clearCookies();
            return false;
        }

        return true;
    }
}
