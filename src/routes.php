<?php

use Src\Controllers\AuthController;
use Src\Controllers\UserController;
use Src\Middlewares\User\UserAuth;
use Src\Router;

$router = new Router();

$router->addGetRoute('/', UserController::class, 'index')->addMidleware(UserAuth::class)->addRouteName("welcome"); // welcome page

// Register Routes
$router->addGetRoute('/register', AuthController::class, 'showRegister')->addRouteName("show-register");
$router->addPostRoute('/register', AuthController::class, 'postRegister');

// Login Routes
$router->addGetRoute('/login', AuthController::class, 'showLogin')->addRouteName("show-login");
$router->addPostROute('/login', AuthController::class, 'postLogin')->addRouteName("post-login");
$router->addPostRoute('/create-user', AuthController::class, 'registerPost');

