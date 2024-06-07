<?php

use Src\Controllers\AuthController;
use Src\Controllers\CourseController;
use Src\Controllers\UserController;
use Src\Middlewares\Admin\AdminAuthMiddleware;
use Src\Middlewares\AuthMiddleware;
use Src\Middlewares\NotAuthMiddleware;
use Src\Router;

$router = new Router();

//--------------------------------------- Unathenticated Routes ---------------------------------------//

// Register Routes
$router->addGetRoute('/register', AuthController::class, 'showRegister')->addMiddleware(NotAuthMiddleware::class)->addRouteName("show-register");
$router->addPostRoute('/register', AuthController::class, 'postRegister')->addMiddleware(NotAuthMiddleware::class);

// Login Routes
$router->addGetRoute('/login', AuthController::class, 'showLogin')->addMiddleware(NotAuthMiddleware::class)->addRouteName("show-login");
$router->addPostRoute('/login', AuthController::class, 'postLogin')->addMiddleware(NotAuthMiddleware::class);

//--------------------------------------- Authenticated Routes ---------------------------------------//

$router->addGetRoute('/', UserController::class, 'index')->addMiddleware(AuthMiddleware::class)->addRouteName("welcome"); // homepage

$router->addPostRoute('/logout', AuthController::class, "logout")->addMiddleware(AuthMiddleware::class)->addRouteName("logout"); // logout 

//--------------------------------------- Admin Routes ---------------------------------------//

// Course Routes
$router->addGetRoute('/admin/course', CourseController::class, "showCourses")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("show-course"); 