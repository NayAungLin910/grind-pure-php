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

        $id = "id";
        $password = "password";
        $name = "name";
        $role = "role";

        if (password_verify($credentials["password"], $user->$password)) { // if password matches

            session_start();

            $_SESSION['auth'] = [
                "id" => $user->$id,
                "name" => $user->$name,
                "role" => $user->$role,
            ];

            session_write_close();

            $router->redirectUsingRouteName("welcome");
        }

        $validator->addError("password", "Wrong password!");
        $validator->flashErrors();
    }
    
    /**
     * Logout user, destroy auth session and redirect back
     */
    public function logout(): void
    {
        session_start();

        if (isset($_SESSION["auth"])) {
            unset($_SESSION["auth"]);
        }

        session_write_close();

        $router = new Router();
        $router->redirectUsingRouteName("welcome");
    }
}
