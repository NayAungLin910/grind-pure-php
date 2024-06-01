<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\User;
use Src\Router;
use Src\Validators\User\UserValidator;

class AuthController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    /**
     * Show register page
     */
    public function showRegister(): void
    {
        $this->render('auth/register');
    }

    /**
     * Handles user registration
     */
    public function postRegister(): void
    {
        $userValidator = new UserValidator();

        $userValidator->checkRequestFields(["name", "email", "password"]);

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $userValidator->nameValidate($name, 'name');
        $userValidator->emailValidate($email, 'email');
        $userValidator->passwordValidate($password, 'password');

        $userValidator->flashOldRequestData([
            "name" => $name,
            "email" => $email,
        ]);
        $userValidator->flashErrors();

        User::create([ // create a new user
            "name" => $name,
            "email" => $email,
            "profile_image" => "/default/images/default_user.jpg",
            "password" => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $this->router->redirectUsingRouteName('show-login');
    }

    /**
     * Show login page
     */
    public function showLogin(): void
    {
        $this->render("auth/login");
    }

    /**
     * Handles login post request
     */
    public function postLogin(): void
    {
        $userValidator = new UserValidator();

        $userValidator->checkRequestFields(["email", "password"]);

        $email = $_POST['email'];
        $password = $_POST['password'];

        $userValidator->emailValidate($email, "email");
        $userValidator->loginPasswordValidate($password, "password");

        $userValidator->flashOldRequestData(["email" => $email]);
        $userValidator->flashErrors();

        User::auth([
            "email" => $email,
            "password" => $password,
        ]);
    }

    public function registerPost(): void
    {
        return;
    }
}
