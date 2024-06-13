<?php

namespace Src\Services;

use Src\DbConnection;
use Src\Models\User;
use Src\Router;
use Src\Validators\FormValidator;

/**
 * Service for authentication
 */
class AuthService
{
    /**
     * Authenticate user
     */
    public function auth(array $credentials): void
    {
        require "../config/bootstrap.php";

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $credentials['email']
        ]);

        $validator = new FormValidator();
        $router = new Router();

        if (!$user) { // if no user with the given email exists

            $validator->addError("email", "The user with the given " . $credentials["email"] . " does not exist.");
            $validator->flashErrors();

            $router->redirectBack();
        }

        if (password_verify($credentials["password"], $user->getPassword())) { // if password matches

            $_SESSION['auth'] = [
                "id" => $user->getId(),
                "name" => $user->getName(),
                "role" => $user->getRole(),
            ];

            if (isset($credentials["remember"])) {
                $this->setCookieAndToken($user->getEmail());
            } else {
                $this->clearCookies();
            }

            $router->redirectUsingRouteName("welcome");
        }

        $validator->addError("password", "Wrong password!");
        $validator->flashErrors();
    }

    /**
     * Get token for authentication
     */
    public function generateToken(int $byteLength = 16): string|null
    {
        $byteString = openssl_random_pseudo_bytes($byteLength);

        $token = $byteString ? bin2hex($byteString) : null;

        return $token;
    }


    /**
     * Setup Cookie and insert a token to remember user
     */
    public function setCookieAndToken(string $email): void
    {
        $randToken = $this->generateToken();

        $randSelectorToken = $this->generateToken();

        $randTokenHashed = password_hash($randToken, PASSWORD_DEFAULT);
        $randSelectorTokenHashed = password_hash($randSelectorToken, PASSWORD_DEFAULT);

        $expiryDate = time() + (10 * 24 * 60 * 60); // 10 days ahead of current time

        setcookie("rand_token", $randToken, $expiryDate);
        setcookie("rand_selector_token", $randSelectorToken, $expiryDate);
        setcookie("email", $email, $expiryDate);

        $this->renewToken($email, $randTokenHashed, $randSelectorTokenHashed);
    }

    /**
     * Insert new token and delete old one
     */
    public function renewToken(string $email, string $randTokenHashed, string $randTokenSelectorHashed): void
    {
        $row = $this->getTokenUsingEmail($email);

        if ($row !== null) $this->deleteToken($row["id"]);  // if an old token exists, delete the token

        $query = "INSERT INTO login_tokens (email, password, selector) VALUES (?, ?, ?)";

        $con = DbConnection::getMySQLConnection(); // get connection

        $statment = $con->prepare($query);
        $statment->bind_param("sss", $email, $randTokenHashed, $randTokenSelectorHashed);
        $statment->execute();
    }

    /**
     * Get token using email
     */
    public  function getTokenUsingEmail(string $email): array|null
    {
        $query = "SELECT * FROM login_tokens WHERE email = ? AND expiry_date >= NOW()"; // select token that has not beeen expired using given email

        $con = DbConnection::getMySQLConnection(); // get connection

        $statment = $con->prepare($query);
        $statment->bind_param("s", $email);
        $statment->execute();

        $result = $statment->get_result();
        $row = $result->fetch_assoc();

        return $row;
    }

    /**
     * Delete token
     */
    public function deleteToken(int $id): void
    {
        $query = "DELETE FROM login_tokens WHERE id = ?";

        $con = DbConnection::getMySQLConnection(); // get connection

        $statment = $con->prepare($query);
        $statment->bind_param("i", $id);
        $statment->exectue();
    }

    /**
     * Clear remember user cookies
     */
    public  function clearCookies(): void
    {
        $oneDayPast = time() - 3600 * 24;

        setcookie("rand_token", "", $oneDayPast);
        setcookie("rand_selector_token", "", $oneDayPast);
        setcookie("email", "", $oneDayPast);
    }

    /**
     * Logout user, destroy auth session and redirect back
     */
    public function logout(): void
    {
        if (isset($_SESSION["auth"])) unset($_SESSION["auth"]);

        $this->clearCookies();
    }
}
