<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Course;
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
        $users = User::select([
            "users.id",
            "users.name",
            "courses.title AS courses_title",
            "courses.id AS courses_id"
        ])->with('courses')->where("users.name", "user1")->getSingle();
        dd($users);

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
        $email = $_POST['email'];
        $password = $_POST['password'];

        $userValidator = new UserValidator();
        $userValidator->emailValidate($email, "email");
        $userValidator->loginPasswordValidate($password, "password");
        $userValidator->flashPreFormData([
            "email" => $email
        ]);
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
