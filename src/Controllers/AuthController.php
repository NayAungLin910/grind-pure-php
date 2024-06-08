<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\User;
use Src\Router;
use Src\Services\FormService;
use Src\Validators\User\UserValidator;

class AuthController extends Controller
{
    public function __construct(private $router = new Router(), private $formService = new FormService())
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

        $userValidator->checkRequestFields(["name", "email", "password", "profile"]);

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $profileImage = $_FILES['profile'];

        $userValidator->nameValidate($name, 'name');
        $userValidator->emailValidate($email, 'email');
        $userValidator->passwordValidate($password, 'password');
        $userValidator->profileImageValidate($profileImage, 'profile image');

        $userValidator->flashOldRequestData([
            "name" => $name,
            "email" => $email,
        ]);
        $userValidator->flashErrors();

        $profileDir = $this->formService->uploadFiles($profileImage, "/images", "profile image");

        dd(true);

        User::create([ // create a new user
            "name" => $name,
            "email" => $email,
            "profile_image" => $profileDir,
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
        $remember = $_POST['remember'] ?? null;

        $userValidator->emailValidate($email, "email");
        $userValidator->loginPasswordValidate($password, "password");

        $userValidator->flashOldRequestData(["email" => $email]);
        $userValidator->flashErrors();

        User::auth([
            "email" => $email,
            "password" => $password,
            "remember" => $remember,
        ]);
    }

    /**
     * Logout the authenticated user
     */
    public function logout(): void
    {
        $user = User::selectAll()->where("id", $_SESSION["auth"]["id"])->getSingle();

        $user::logout();

        $this->router->redirectUsingRouteName("show-login");
    }
}
