<?php

use Src\Controllers\AuthController;
use Src\Controllers\BinController;
use Src\Controllers\CourseController;
use Src\Controllers\TagController;
use Src\Controllers\Bin\TagController as BinTagController;
use Src\Controllers\UserController;
use Src\Middlewares\Admin\AdminAuthMiddleware;
use Src\Middlewares\AuthMiddleware;
use Src\Middlewares\NotAuthMiddleware;
use Src\Router;

$router = new Router();

//--------------------------------------- Unathenticated Routes ---------------------------------------//
//---------------- Register -------------//
$router->addGetRoute('/register', AuthController::class, 'showRegister')->addMiddleware(NotAuthMiddleware::class)->addRouteName("show-register");
$router->addPostRoute('/register', AuthController::class, 'postRegister')->addMiddleware(NotAuthMiddleware::class);

// --------------- Course --------------//
$router->addGetRoute('/admin/course/single', CourseController::class, "showSingleCourse")->addRouteName('show-single-course');

//---------------- Login -------------//
$router->addGetRoute('/login', AuthController::class, 'showLogin')->addMiddleware(NotAuthMiddleware::class)->addRouteName("show-login");
$router->addPostRoute('/login', AuthController::class, 'postLogin')->addMiddleware(NotAuthMiddleware::class);
$router->addGetRoute('/', UserController::class, 'index')->addMiddleware(AuthMiddleware::class)->addRouteName("welcome"); // homepage

//--------------------------------------- Authenticated Routes ---------------------------------------//
$router->addPostRoute('/logout', AuthController::class, "logout")->addMiddleware(AuthMiddleware::class)->addRouteName("logout"); // logout 

//--------------------------------------- Admin Routes -----------------------------//
//---------------- Tags -------------//
$router->addGetRoute('/admin/tag', TagController::class, "showTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('show-tag');
$router->addGetRoute('/admin/tag/create', TagController::class, "showCreateTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("show-tag-create");
$router->addPostRoute('/admin/tag/create', TagController::class, 'postCreateTag')->addMiddleware(AdminAuthMiddleware::class)->addRouteName("post-tag-create");
$router->addPostRoute('/admin/tag/delete', TagController::class, "postDeleteTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-tag-delete');
$router->addGetRoute('/admin/tag/edit', TagController::class, "showEditTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('show-tag-edit');
$router->addPostRoute('/admin/tag/edit', TagController::class, "postEditTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-tag-edit');

//------------Course Routes-----------//
$router->addGetRoute('/admin/course', CourseController::class, "showCourses")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("show-course");
$router->addGetRoute('/admin/course/create', CourseController::class, "showCourseCreate")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("show-course-create");
$router->addPostRoute('/admin/course/create', CourseController::class, 'postCourseCreate')->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-course-create');

//------------------------ Bin Routes --------------------//
//----------- Tag Routes ---------//
$router->addGetRoute('/admin/bin/tag', BinTagController::class, "showBinTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('show-bin-tag');
$router->addPostRoute('/admin/bin/tag', BinTagController::class, "postDeleteBinTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-bin-tag-delete');
$router->addPostRoute('/admin/bin/tag/recover', BinTagController::class, "postBinTagRecover")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-bin-tag-recover');
