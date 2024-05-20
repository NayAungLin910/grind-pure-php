<?php

use Src\Controllers\AuthConroller;
use Src\Controllers\UserController;
use Src\Middlewares\Users\UserAuth;
use Src\Router;

$router = new Router();
$router->addGetRoute('/', UserController::class, 'index')->addMidleware(UserAuth::class);
$router->addGetRoute('/register', AuthConroller::class, 'showRegister')->addMidleware(UserAuth::class);
$router->addPostRoute('/create-user', AuthConroller::class, 'registerPost');
