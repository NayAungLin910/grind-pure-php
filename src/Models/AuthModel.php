<?php

namespace Src\Models;

use Src\Router;
use Src\Validators\FormValidator;

class AuthModel extends Model
{
    /**
     * Authenticate the model using given email and password
     */
    public static function auth(array $credentials)
    {
        $user = static::selectAll()->where("email", $credentials["email"])->getAuth();
        $validator = new FormValidator();
        $router = new Router();

        if (!$user) { // if no user with the given email exists

            $validator->addError("email", "The user with the given " . $credentials["email"] . " does not exist.");
            $validator->flashErrors();

            $router->redirectBack();
        }

        if (password_verify($credentials["password"], $user->password)) { // if password matches

            $_SESSION['auth'] = [
                "id" => $user->id,
                "name" => $user->name,
                "role" => $user->role,
            ];

            if (isset($credentials["remember"])) {
                static::setCookieAndToken($user->email);
            } else {
                static::clearCookies();
            }

            $router->redirectUsingRouteName("welcome");
        }

        $validator->addError("password", "Wrong password!");
        $validator->flashErrors();
    }

    /**
     * Setup Cookie and insert a token to remember user
     */
    public static function setCookieAndToken(string $email): void
    {
        $randToken = static::generateToken();

        $randSelectorToken = static::generateToken();

        $randTokenHashed = password_hash($randToken, PASSWORD_DEFAULT);
        $randSelectorTokenHashed = password_hash($randSelectorToken, PASSWORD_DEFAULT);

        $expiryDate = time() + (10 * 24 * 60 * 60); // 10 days ahead of current time

        setcookie("rand_token", $randToken, $expiryDate);
        setcookie("rand_selector_token", $randSelectorToken, $expiryDate);
        setcookie("email", $email, $expiryDate);

        static::renewToken($email, $randTokenHashed, $randSelectorTokenHashed);
    }

    /**
     * Logout user, destroy auth session and redirect back
     */
    public static function logout(): void
    {

        if (isset($_SESSION["auth"])) unset($_SESSION["auth"]);

        static::clearCookies();
    }

    /**
     * Get token for authentication
     */
    public static function generateToken(int $byteLength = 16): string|null
    {
        $byteString = openssl_random_pseudo_bytes($byteLength);

        $token = $byteString ? bin2hex($byteString) : null;

        return $token;
    }

    /**
     * Insert new token and delete old one
     */
    public static function renewToken(string $email, string $randTokenHashed, string $randTokenSelectorHashed): void
    {
        $row = static::getTokenUsingEmail($email);

        if ($row !== null) static::deleteToken($row["id"]);  // if an old token exists, delete the token

        static::$query = "INSERT INTO login_tokens (email, password, selector) VALUES (?, ?, ?)";
        static::$values = [$email, $randTokenHashed, $randTokenSelectorHashed];

        static::runQuery();
        static::$result = null;
    }

    /**
     * Get token using email
     */
    public static function getTokenUsingEmail(string $email): array|null
    {
        static::$query = "SELECT * FROM login_tokens WHERE email = ? AND expiry_date >= NOW()"; // select token that has not beeen expired using given email
        static::$values = [$email];
        static::runQuery();

        $row = static::$result->fetch_assoc();
        static::resetStaticValues();

        return $row;
    }

    /**
     * Delete token
     */
    public static function deleteToken(int $id): void
    {
        static::$query = "DELETE FROM login_tokens WHERE id = ?";
        static::$values = [$id];

        static::runQuery();
        static::resetStaticValues();
    }

    /**
     * Clear remember user cookies
     */
    public static function clearCookies(): void
    {
        $oneDayPast = time() - 3600 * 24;

        setcookie("rand_token", "", $oneDayPast);
        setcookie("rand_selector_token", "", $oneDayPast);
        setcookie("email", "", $oneDayPast);
    }
}
