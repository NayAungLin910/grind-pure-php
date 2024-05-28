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

        $password = "password";
        $name = "name";
        $id = "id";

        if (password_verify($credentials["password"], $user->$password)) { // if password matches

            session_start();

            $_SESSION['auth'] = TRUE;
            $_SESSION['name'] = $user->$name;
            $_SESSION['id'] = $user->$id;

            $router->redirectUsingRouteName("welcome");
        }

        $validator->addError("password", "Wrong password!");
        $validator->flashErrors();
    }
}
