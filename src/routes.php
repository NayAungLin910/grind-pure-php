<?php

use Src\Controllers\AnswerController;
use Src\Controllers\AuthController;
use Src\Controllers\CourseController;
use Src\Controllers\TagController;
use Src\Controllers\Bin\TagController as BinTagController;
use Src\Controllers\Bin\CourseController as BinCourseController;
use Src\Controllers\ProfileController;
use Src\Controllers\QuestionController;
use Src\Controllers\SectionController;
use Src\Controllers\StepController;
use Src\Controllers\UserController;
use Src\Middlewares\Admin\AdminAuthMiddleware;
use Src\Middlewares\AuthMiddleware;
use Src\Middlewares\NotAuthMiddleware;
use Src\Router;

$router = new Router();

//--------------- Courses ---------------//
$router->addGetRoute('/courses', CourseController::class, "showPublicCourse")->addRouteName('show-public-course');
$router->addGetRoute('/course/specific', CourseController::class, "showPublicSpecCourse")->addRouteName('show-public-spec-course');

//--------------------------------------- Unathenticated Only  Routes ---------------------------------------//
//---------------- Register -------------//
$router->addGetRoute('/register', AuthController::class, 'showRegister')->addMiddleware(NotAuthMiddleware::class)->addRouteName("show-register");
$router->addPostRoute('/register', AuthController::class, 'postRegister')->addMiddleware(NotAuthMiddleware::class);

//---------------- Login -------------//
$router->addGetRoute('/login', AuthController::class, 'showLogin')->addMiddleware(NotAuthMiddleware::class)->addRouteName("show-login");
$router->addPostRoute('/login', AuthController::class, 'postLogin')->addMiddleware(NotAuthMiddleware::class);
$router->addGetRoute('/', UserController::class, 'index')->addMiddleware(AuthMiddleware::class)->addRouteName("welcome"); // homepage

//--------------------------------------- Authenticated Routes ---------------------------------------//
$router->addPostRoute('/logout', AuthController::class, "logout")->addRouteName("logout"); // logout 

// ---------- Course ----------------//
$router->addPostRoute('/course/specific/enroll', CourseController::class, "postEnrollCourse")->addRouteName('post-course-enroll');

//----------- Step ----------------//
$router->addPostRoute('/course/step/complete', StepController::class, "postStepComplete")->addMiddleware(AuthMiddleware::class)->addRouteName('post-step-complete');

//---------- Profile -------------//
$router->addGetRoute('/profile', ProfileController::class, "showProfile")->addRouteName('profile');
$router->addPostRoute('/profile/save', ProfileController::class, "postProfile")->addMiddleware(AuthMiddleware::class)->addRouteName('post-profile');

//-------------- Change Password ------//
$router->addPostRoute('/profile/password-change', ProfileController::class, "postPasswordChange")->addMiddleware(AuthMiddleware::class)->addRouteName('post-password-change');

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
$router->addGetRoute('/admin/course/single', CourseController::class, "showSingleCourse")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('show-single-course');
$router->addGetRoute('/admin/course/edit', CourseController::class, "showCourseEdit")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('show-course-edit');
$router->addPostRoute('/admin/course/edit', CourseController::class, "postCourseEdit")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-course-edit');
$router->addPostRoute('/admin/course/bin', CourseController::class, "postCourseBin")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-course-bin');

//-------- Section Routes -----//
$router->addPostRoute('/admin/section/create', SectionController::class, "postSectionCreate")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-section-create');
$router->addPostRoute('/admin/section/update', SectionController::class, "postSectionEdit")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-section-edit');
$router->addPostRoute('/admin/section/delete', SectionController::class, "postSectionDelete")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-section-delete');

//-------- Step -------------//
$router->addGetRoute('/admin/step/create-get', StepController::class, "showStepCreate")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("show-step-create");
$router->addPostRoute('/admin/step/create', StepController::class, "postStepCreate")->addMiddleware(AdminAuthMiddleware::class)->addRouteName("post-step-create");
$router->addPostRoute('/admin/step/delete', StepController::class, "postStepDelete")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-step-delete');
$router->addGetRoute('/admin/step/edit-get', StepController::class, "showStepEdit")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('show-step-edit');
$router->addPostRoute('/admin/step/edit', StepController::class, "postStepEdit")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-step-edit');

//-------- Question -------------// 
$router->addPostRoute('/admin/question/create', QuestionController::class, "postQuestionCreate")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-question-create');
$router->addPostRoute('/admin/question/create-edit', QuestionController::class, "postQuestionEdit")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-question-edit');
$router->addPostRoute('/admin/question/delete', QuestionController::class, 'postQuestionDelete')->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-question-delete');

//--------- Answer --------------//
$router->addPostRoute('/admin/answer/create', AnswerController::class, "postAnswerCreate")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-answer-create');
$router->addPostRoute('/admin/answer/edit', AnswerController::class, "postAnswerEdit")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-answer-edit');
$router->addPostRoute('/admin/answer/delete', AnswerController::class, "postAnswerDelete")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-answer-delete');

//------------------------ Bin Routes --------------------//

//----------- Tag Routes ---------//
$router->addGetRoute('/admin/bin/tag', BinTagController::class, "showBinTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('show-bin-tag');
$router->addPostRoute('/admin/bin/tag', BinTagController::class, "postDeleteBinTag")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-bin-tag-delete');
$router->addPostRoute('/admin/bin/tag/recover', BinTagController::class, "postBinTagRecover")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-bin-tag-recover');

//----------- Courses------------//
$router->addGetRoute('/admin/bin/course', BinCourseController::class, "showBinCourse")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('show-bin-course');
$router->addPostRoute('/admin/bin/course/recover', BinCourseController::class, "postBinCourseRecover")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-bin-course-recover');
$router->addPostRoute('/admin/bin/course/delete', BinCourseController::class, "postBinCourseDelete")->addMiddleware(AdminAuthMiddleware::class)->addRouteName('post-bin-course-delete');
