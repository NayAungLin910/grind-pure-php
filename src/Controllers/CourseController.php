<?php

namespace Src\Controllers;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Src\Controller;
use Src\Models\Course;
use Src\Models\Tag;
use Src\Models\User;
use Src\Router;
use Src\Services\AuthService;
use Src\Services\FormService;
use Src\Validators\Course\CourseValidator;
use Src\Validators\FormValidator;

class CourseController extends Controller
{

    public function __construct(private $authService = new AuthService(), private $router = new Router())
    {
    }

    /**
     * Shows paginationDq$paginationDql view
     */
    public function showCourses(): void
    {
        $title = isset($_GET['title']) ? $_GET['title'] : "";
        $pageSize = isset($_GET['page-size']) && $_GET['page-size'] > 0 ? $_GET['page-size'] : 10;
        $page =  isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
        $created_by_me = isset($_GET['created_by_me']) ? $_GET['created_by_me'] : null;
        $sortByOldest = isset($_GET['oldest']) ? $_GET['oldest'] : null;
        $tagSelected = isset($_GET['tags']) ? $_GET['tags'] : null;

        require "../config/bootstrap.php";

        $paginationDql = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->join('c.user', 'u')
            ->where('c.id > 0');

        if ($title !== "") $paginationDql->andWhere('c.title LIKE :title')->setParameter('title', "%$title%");

        if ($created_by_me) $paginationDql->andWhere('u.id = :id')->setParameter('id', $_SESSION['auth']['id']);

        if ($sortByOldest) {
            $paginationDql = $paginationDql->orderBy('c.created_at', 'DESC');
        } else {
            $paginationDql = $paginationDql->orderBy('c.created_at', 'ASC');
        }

        if ($tagSelected) {
            $paginationDql->leftJoin('c.tags', 't')->andWhere('t.id IN (:tags)')->setParameter('tags', ['tags' => $tagSelected]);
        }

        $paginationDql->andWhere('c.deleted = false');
        $paginationDql->setFirstResult(($page - 1) * $pageSize);
        $paginationDql->setMaxResults($pageSize);

        $tags = $entityManager->createQueryBuilder()
            ->select('t')
            ->from(Tag::class, 't')
            ->andWhere('t.deleted = false')
            ->getQuery()
            ->getResult();

        $paginator = new Paginator($paginationDql);

        $courses = [];

        foreach ($paginator as $course) {
            $courses[] = $course;
        }

        $totalItems = count($paginator); // all the rows of the table filtered from paginationQuery
        $totalPages = ceil($totalItems / $pageSize);

        if (is_array($tagSelected)) $tagSelected = array_map('intval', $tagSelected);

        $formValidator = new FormValidator();
        $formValidator->flashOldRequestData(compact('title', 'created_by_me', 'sortByOldest', 'tagSelected'));

        $this->render("admin/course/index", compact('courses', 'pageSize', 'page', 'totalItems', 'totalPages', 'tags'));
    }

    /**
     * Show course create view
     */
    public function showCourseCreate(): void
    {
        require "../config/bootstrap.php";

        $tags = $entityManager->createQueryBuilder()
            ->select('t')
            ->from(Tag::class, 't')
            ->where('t.deleted = false')
            ->getQuery()
            ->getResult();

        $this->render("admin/course/create", compact('tags'));
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
        $tags = isset($_POST['tags']) ? $_POST['tags'] : null;

        $courseValidator->titleValidate($title, 'title');
        $courseValidator->descriptionValidate($description, 'description');
        $courseValidator->imageValidate($image, 'image');
        $courseValidator->tagValidate($tags, 'tag');

        if (is_array($tags)) $tags = array_map('intval', $tags);

        $courseValidator->flashOldRequestData(compact('title', 'description', 'tags'));
        $courseValidator->flashErrors();

        $formService = new FormService();

        require "../config/bootstrap.php";

        $user = $entityManager->getRepository(User::class)->findOneById($_SESSION['auth']['id']);

        $tags = $entityManager->getRepository(Tag::class)->findBy(['id' => $_POST['tags']]);

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

        foreach ($tags as $tag) {
            $course->getTags()->add($tag);
            $tag->getCourses()->add($course);
        }

        $entityManager->persist($course);
        $entityManager->flush();

        $courseValidator->resetOldRequestData();

        $this->router->notificationSessionFlash('noti-success', 'Course created successfully!');
        $this->router->redirectUsingRouteName('show-course-create');
    }

    /**
     * Show specific course
     */
    public function showSingleCourse(): void
    {
        var_dump($_GET);
    }
}
