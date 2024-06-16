<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Course;
use Src\Models\User;
use Src\Router;
use Src\Services\AuthService;
use Src\Services\FormService;
use Src\Validators\Course\CourseValidator;

class CourseController extends Controller
{

    public function __construct(private $authService = new AuthService(), private $router = new Router())
    {
    }

    /**
     * Shows courses view
     */
    public function showCourses(): void
    {
        $this->render("admin/course/index");
    }

    /**
     * Show course create view
     */
    public function showCourseCreate(): void
    {
        $this->render("admin/course/create");
    }

    /**
     * Post course create
     */
    public function postCourseCreate(): void
    {
        $courseValidator = new CourseValidator();

        $courseValidator->checkRequestFields(['title', 'description', 'image']);

        $title = $_POST['title'];
        $description = $_POST['description'];
        $image = $_FILES['image'];

        $courseValidator->titleValidate($title, 'title');
        $courseValidator->descriptionValidate($description, 'description');
        $courseValidator->imageValidate($image, 'image');

        $courseValidator->flashOldRequestData([
            "title" => $title,
            "description" => $description,
        ]);
        $courseValidator->flashErrors();

        $formService = new FormService();

        require "../config/bootstrap.php";

        $user = $entityManager->getRepository(User::class)->findOneById($_SESSION['auth']['id']);

        if (!$user) {
            $courseValidator->addError("user-not-found", "User not found!");
            $courseValidator->flashErrors();
        }

        $imageDir = $formService->uploadFiles($image, "/images", 'image');

        $course = new Course;
        $course->setUser($user);
        $course->setTitle($title);
        $course->setDescription($description);
        $course->setImage($imageDir);

        $entityManager->persist($course);
        $entityManager->flush();

        $courseValidator->resetOldRequestData();

        $this->router->redirectUsingRouteName('show-course-create');
    }
}
