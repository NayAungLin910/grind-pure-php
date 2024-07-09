<?php

namespace Src\Controllers;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Src\Controller;
use Src\Models\Course;
use Src\Models\Section;
use Src\Models\Step;
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
            ->leftJoin('c.tags', 't')
            ->andWhere('c.id > 0');

        if ($title !== "") $paginationDql->andWhere('c.title LIKE :title')->setParameter('title', "%$title%");

        if ($created_by_me) $paginationDql->andWhere('u.id = :id')->setParameter('id', $_SESSION['auth']['id']);

        if ($sortByOldest) {
            $paginationDql = $paginationDql->orderBy('c.id', 'ASC');
        } else {
            $paginationDql = $paginationDql->orderBy('c.id', 'DESC');
        }

        if ($tagSelected) {
            $paginationDql->andWhere('t.id IN (:tags)')->setParameter('tags', ['tags' => $tagSelected]);
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
     * Show course edit view
     */
    public function showCourseEdit(): void
    {
        $courseValidator = new CourseValidator();

        $courseValidator->checkRequestFields(['title']);

        $title = $_GET['title'];

        require "../config/bootstrap.php";

        try {
            $course = $entityManager->createQueryBuilder()
                ->select('c')
                ->from(Course::class, 'c')
                ->where('c.title = :title')->setParameter('title', $title)
                ->leftJoin('c.tags', 't')
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) { // if no result 
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $tags = $entityManager->createQueryBuilder()
            ->select('t')
            ->from(Tag::class, 't')
            ->where('t.deleted = false')
            ->getQuery()
            ->getResult();

        $this->render("admin/course/edit", compact('course', 'tags'));
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
     * Handles post request to update the existing course
     */
    public function postCourseEdit(): void
    {
        $courseValidator = new CourseValidator();

        $courseValidator->checkRequestFields(['title', 'description', 'course-id']);

        $course_id = $_POST['course-id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $image = isset($_FILES['image']) && $_FILES['image']['name'] !== "" ? $_FILES['image'] : null;

        $tags = isset($_POST['tags']) ? $_POST['tags'] : null;

        $courseValidator->titleEditValidate($title, 'title', $course_id);
        $courseValidator->descriptionValidate($description, 'description');
        if ($image) $courseValidator->imageValidate($image, 'image');
        $courseValidator->tagValidate($tags, 'tag');

        if (is_array($tags)) $tags = array_map('intval', $tags);

        $courseValidator->flashErrors();

        $formService = new FormService();

        require "../config/bootstrap.php";

        try {
            $course = $entityManager->createQueryBuilder()
                ->select('c')
                ->from(Course::class, 'c')
                ->leftJoin('c.tags', 't')
                ->where('c.id = :course_id')->setParameter('course_id', $course_id)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) { // if no result 
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $tags = $entityManager->getRepository(Tag::class)->findBy(['id' => $_POST['tags']]);

        $imageDir = $course->getImage();

        if ($image) { // if new image is selected
            $formService->deleteFile($course->getImage());
            $imageDir = $formService->uploadFiles($image, "/images", 'image');
        }

        $course->setTitle($title);
        $course->setDescription($description);
        $course->setImage($imageDir);

        foreach ($course->getUndeletedTags() as $t) { // removes old many-to-many relationship of tags
            $course->getTags()->removeElement($t);
            $t->getCourses()->removeElement($course);
        }

        foreach ($tags as $tag) {
            $course->getTags()->add($tag);
            $tag->getCourses()->add($course);
        }

        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Course updated successfully!');
        $this->router->redirectUsingRouteName('show-course');
    }

    /**
     * Show specific course
     */
    public function showSingleCourse(): void
    {
        if (!isset($_GET['title'])) {
            $this->router->notificationSessionFlash('noti-danger', 'Id not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        require "../config/bootstrap.php";

        $title = $_GET['title'];

        try {
            $course = $entityManager->createQueryBuilder()
                ->select('c')
                ->from(Course::class, 'c')
                ->leftJoin('c.tags', 't')
                ->leftJoin('c.user', 'u')
                ->leftJoin('c.sections', 's')
                ->where('c.title = :title')->setParameter('title', $title)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) { // if no result 
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $step_id = null;
        $section = null;

        if (isset($_GET['edit-section-id'])) $step_id = $_GET['edit-section-id'];

        if ($step_id) {
            try {
                $section = $entityManager->createQueryBuilder()
                    ->select('s')
                    ->from(Section::class, 's')
                    ->andWhere('s.id = :step_id')->setParameter('step_id', $step_id)
                    ->leftJoin('s.course', 'c')
                    ->leftJoin('s.steps', 'st')
                    ->andWhere('c.title = :title')->setParameter('title', $title)
                    ->getQuery()
                    ->getSingleResult();
            } catch (NoResultException $e) {
                $this->router->notificationSessionFlash('noti-danger', 'Step not found!');
                $this->router->redirectUsingRouteName('show-course');
            }
        }

        $this->render('/admin/course/single', compact('course', 'section'));
    }

    /**
     * Moves course into bin
     */
    public function postCourseBin(): void
    {
        if (!isset($_POST['course-id'])) {
            $this->router->notificationSessionFlash('noti-danger', 'Course Id not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        require "../config/bootstrap.php";

        $courseId = $_POST['course-id'];

        $course = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->where('c.id = :c_id')->setParameter('c_id', $courseId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$course) {
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $course->setDeleted(true);

        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', "Course moved to bin successfully!");
        $this->router->redirectUsingRouteName('show-course');
    }

    /**
     * Show public courses
     */
    public function showPublicCourse(): void
    {
        $title = isset($_GET['title']) ? $_GET['title'] : "";
        $pageSize = isset($_GET['page-size']) && $_GET['page-size'] > 0 ? $_GET['page-size'] : 10;
        $page =  isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
        $sortByOldest = isset($_GET['oldest']) ? $_GET['oldest'] : null;
        $tagSelected = isset($_GET['tags']) ? $_GET['tags'] : null;

        require "../config/bootstrap.php";

        $paginationDql = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->join('c.user', 'u')
            ->leftJoin('c.enrollments', 'e')
            ->leftJoin('c.tags', 't')
            ->andWhere('c.id > 0');

        if ($title !== "") $paginationDql->andWhere('c.title LIKE :title')->setParameter('title', "%$title%");

        if ($sortByOldest) {
            $paginationDql = $paginationDql->orderBy('c.id', 'ASC');
        } else {
            $paginationDql = $paginationDql->orderBy('c.id', 'DESC');
        }

        if ($tagSelected) {
            $paginationDql->andWhere('t.id IN (:tags)')->setParameter('tags', ['tags' => $tagSelected]);
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

        $this->render("public/course", compact('courses', 'pageSize', 'page', 'totalItems', 'totalPages', 'tags'));
    }

    /**
     * Show public specific course
     */
    public function showPublicSpecCourse(): void
    {
        if (!isset($_GET['title'])) {
            $this->router->notificationSessionFlash('noti-danger', 'Id not found!');
            $this->router->redirectUsingRouteName('show-public-course');
        }

        $title = $_GET['title'];

        $currentStepId = isset($_GET['current-step']) ? $_GET['current-step'] : null;

        require "../config/bootstrap.php";

        try {
            $course = $entityManager->createQueryBuilder()
                ->select('c')
                ->from(Course::class, 'c')
                ->leftJoin('c.tags', 't')
                ->leftJoin('c.user', 'u')
                ->leftJoin('c.sections', 's')
                ->leftJoin('s.steps', 'st')
                ->leftJoin('c.enrollments', 'e')
                ->where('c.title = :title')->setParameter('title', $title)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) { // if no result 
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectUsingRouteName('show-public-course');
        }

        $currentStep = null;

        if ($currentStepId) {
            $currentStep = $entityManager->createQueryBuilder()
                ->select('s')
                ->from(Step::class, 's')
                ->leftJoin('s.users', 'u')
                ->leftJoin('s.section', 'sec')
                ->leftJoin('s.questions', 'q')
                ->leftJoin('q.answers', 'a')
                ->leftJoin('sec.course', 'c')
                ->where('s.id = :s_id')->setParameter('s_id', $currentStepId)
                ->andWhere('c.id = :c_id')->setParameter('c_id', $course->getId())
                ->getQuery()
                ->getOneOrNullResult();

            if (!$currentStep) {
                $this->router->notificationSessionFlash('noti-danger', 'Step not found!');
                $this->router->redirectUsingRouteName('show-public-course');
            }
        } else {
            try {
                $currentStep = $entityManager->createQueryBuilder()
                    ->select('s')
                    ->from(Step::class, 's')
                    ->leftJoin('s.users', 'u')
                    ->leftJoin('s.section', 'sec')
                    ->leftJoin('s.questions', 'q')
                    ->leftJoin('sec.course', 'c')
                    ->leftJoin('q.answers', 'a')
                    ->where('c.id = :c_id')->setParameter('c_id', $course->getId())
                    ->orderBy('s.priority', 'ASC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getSingleResult();
            } catch (NoResultException $e) {
                $this->router->notificationSessionFlash('noti-danger', 'Step not found!');
                $this->router->redirectUsingRouteName('show-public-course');
            }
        }

        $this->render("public/spec-course", compact('course', 'section', 'currentStep'));
    }

    /**
     * Post enroll course
     */
    public function postEnrollCourse(): void
    {
        $courseValidator = new CourseValidator();

        $courseValidator->checkRequestFields(['course-id']);

        $courseId = $_POST['course-id'];

        $courseValidator->checkInteger($courseId, 'course-id');
        $courseValidator->flashErrors();

        require "../config/bootstrap.php";

        $course = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, "c")
            ->leftJoin('c.users', 'u')
            ->where('c.id = :c_id')->setParameter('c_id', $courseId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$course) {
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectUsingRouteName('show-public-course');
        }

        if (!isset($_SESSION['auth']['id'])) {
            $this->router->notificationSessionFlash('noti-danger', 'User not found!');
            $this->router->redirectUsingRouteName('show-login');
        }

        $user = $entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, "u")
            ->leftJoin('u.enrolledCourses', 'ec')
            ->where('u.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id'])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            $authService = new AuthService();
            $authService->logout();

            $this->router->notificationSessionFlash('noti-danger', 'User not found!');
            $this->router->redirectUsingRouteName('show-login');
        }

        $user->getEnrolledCourses()->add($course);
        $course->getUsers()->add($user);

        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Enrolled successfully!');
        $route = $this->router->getRouteUsingRouteName('show-public-spec-course') . "?title=" . $course->getTitle();
        $this->router->redirectTo($route);
    }
}
