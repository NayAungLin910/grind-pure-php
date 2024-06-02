<?php

use Src\Controllers\AuthController;
use Src\Controllers\UserController;
use Src\Middlewares\User\UserAuthMiddleware;
use Src\Middlewares\User\UserNotAuthMiddleware;
use Src\Router;

$router = new Router();

//--------------------------------------- Unathenticated Routes ---------------------------------------//

// Register Routes
$router->addGetRoute('/register', AuthController::class, 'showRegister')->addMiddleware(UserNotAuthMiddleware::class)->addRouteName("show-register");
$router->addPostRoute('/register', AuthController::class, 'postRegister')->addMiddleware(UserNotAuthMiddleware::class);

// Login Routes
$router->addGetRoute('/login', AuthController::class, 'showLogin')->addMiddleware(UserNotAuthMiddleware::class)->addRouteName("show-login");
$router->addPostRoute('/login', AuthController::class, 'postLogin')->addMiddleware(UserNotAuthMiddleware::class);

//--------------------------------------- Authenticated Routes ---------------------------------------//

$router->addGetRoute('/', UserController::class, 'index')->addMiddleware(UserAuthMiddleware::class)->addRouteName("welcome"); // homepage

$router->addPostRoute('/logout', AuthController::class, "logout")->addMiddleware(UserAuthMiddleware::class)->addRouteName("logout"); // logout 
