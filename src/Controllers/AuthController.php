<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Enums\ModelSQLEnum;
use Src\Models\User;
use Src\Router;
use Src\Validators\User\UserValidator;

class AuthController extends Controller
{
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
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $userValidator = new UserValidator();
        $userValidator->nameValidate($name, 'name');
        $userValidator->emailValidate($email, 'email');
        $userValidator->passwordValidate($password, 'password');

        $userValidator->flashErrors(); // session flash errors

        User::create([ // create a new user
            "name" => $name,
            "email" => $email,
            "profile_image" => "/default/images/default_user.jpg",
            "password" => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $this->redirectUsingRouteName('show-login');
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
        $email = $_POST['email'];
        $password = $_POST['password'];

        $userValidator = new UserValidator();
        $userValidator->emailValidate($email, "email");
        $userValidator->loginPasswordValidate($password, "password");
        $userValidator->flashErrors(); // session flash errors

        // $user = User::select(['name', 'email'])->getLatestRow();

    }

    public function registerPost(): void
    {
        return;
    }
}
