<?php

use Src\Controllers\AuthController;
use Src\Controllers\CourseController;
use Src\Controllers\TagController;
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
$router->addGetRoute('/', UserController::class, 'index')->addMiddleware(AuthMiddleware::class)->addRouteName("welcome"); // homepage

//--------------------------------------- Authenticated Routes ---------------------------------------//


$router->addPostRoute('/logout', AuthController::class, "logout")->addMiddleware(AuthMiddleware::class)->addRouteName("logout"); // logout 

//--------------------------------------- Admin Routes ---------------------------------------//

//---------------- Tags -------------//
$router->addGetRoute('/admin/tag/create', TagController::class, "showCreateTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("show-tag-create");
$router->addPostRoute('/admin/tag/create', TagController::class, 'postCreateTag')->addMiddleware(AdminAuthMiddleware::class)->addRouteName("post-tag-create");

//------------Course Routes-----------//
$router->addGetRoute('/admin/course', CourseController::class, "showCourses")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("show-course");
$router->addGetRoute('/admin/course/create', CourseController::class, "showCourseCreate")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("show-course-create");
$router->addPostRoute('/admin/course/create', CourseController::class, 'postCourseCreate')->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-course-create');
