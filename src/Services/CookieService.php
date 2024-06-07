<?php

namespace Src\Services;

use Src\Models\AuthModel;
use Src\Models\User;

class CookieService
{

    /**
     * Check if the login remember cookie exist and is legitimate
     */
    public function confirmRememberLoginCookie(): bool
    {
        if (empty($_COOKIE["email"]) || empty($_COOKIE["rand_token"]) || empty($_COOKIE["rand_selector_token"])) { // if one of the cookies is empty \
            AuthModel::clearCookies();
            return false;
        }
        
        $user = User::selectAll()->where('email', $_COOKIE["email"])->getSingle();
        
        if (!$user) {
            AuthModel::clearCookies();
            return false;
        }

        $token = $user::getTokenUsingEmail($user->email);
        
        if (!$token) {
            $user::clearCookies();
            return false;
        }
        
        $randTokenVerify = password_verify($_COOKIE["rand_token"], $token["password"]);
        $randSelectorTokenVerify = password_verify($_COOKIE["rand_selector_token"], $token["selector"]);
        
        if (!$randTokenVerify || !$randSelectorTokenVerify) { // if the token or the selector token is not legitimate
            $user::clearCookies();
            return false;
        }

        return true;
    }
}
