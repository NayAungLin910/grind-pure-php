<?php

namespace Src\Controllers\Bin;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Src\Controller;
use Src\Models\Course;
use Src\Router;
use Src\Services\FormService;
use Src\Validators\FormValidator;

class CourseController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    /**
     * Show courses in bin
     */
    public function showBinCourse(): void
    {
        $name = isset($_GET['name']) ? $_GET['name'] : "";

        $pageSize = isset($_GET['page-size']) && $_GET['page-size'] > 0 ? $_GET['page-size'] : 10;
        $page =  isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
        $created_by_me = isset($_GET['created_by_me']) ? $_GET['created_by_me'] : null;
        $sortByOldest = isset($_GET['oldest']) ? $_GET['oldest'] : null;

        require "../config/bootstrap.php";

        $paginationDql = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->join('c.user', 'u');

        if ($name !== "") $paginationDql->where('c.title LIKE :title')->setParameter('title', "%$name%");

        if ($created_by_me) $paginationDql->where('u.id = :id')->setParameter('id', $_SESSION['auth']['id']);

        if ($sortByOldest) {
            $paginationDql = $paginationDql->orderBy('c.created_at', 'ASC');
        } else {
            $paginationDql = $paginationDql->orderBy('c.created_at', 'DESC');
        }

        $paginationDql->andWhere('c.deleted = true');
        $paginationDql->setFirstResult(($page - 1) * $pageSize);
        $paginationDql->setMaxResults($pageSize);

        $paginator = new Paginator($paginationDql);

        $courses = [];

        foreach ($paginator as $course) {
            $courses[] = $course;
        }

        $totalItems = count($paginator); // all the rows of the table filtered from paginationQuery
        $totalPages = ceil($totalItems / $pageSize);

        $formValidator = new FormValidator();
        $formValidator->flashOldRequestData(compact('name', 'created_by_me', 'sortByOldest'));

        $this->render('admin/bin/course/index', compact('courses', 'pageSize', 'page', 'totalItems', 'totalPages'));
    }

    /**
     * Recover course
     */
    public function postBinCourseRecover(): void
    {
        $formValidator = new FormValidator();

        $formValidator->checkRequestFields(['recover-id']);

        $recoverId = $_POST['recover-id'];

        $formValidator->checkInteger($recoverId, 'recover-id');

        require "../config/bootstrap.php";

        $course = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->where('c.id = :c_id')->setParameter('c_id', $recoverId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$course) {
            $this->router->notificationSessionFlash('noti-success', 'Course not found!');
            $this->router->redirectBack();
        }

        $course->setDeleted(false);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Course recovered successfully!');
        $this->router->redirectUsingRouteName('show-bin-course');
    }

    /**
     * Deletes course
     */
    public function postBinCourseDelete(): void
    {
        $formValidator = new FormValidator();

        $formValidator->checkRequestFields(['delete-id']);

        $deleteId = $_POST['delete-id'];

        $formValidator->checkInteger($deleteId, 'delete-id');

        require "../config/bootstrap.php";

        $course = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->leftJoin('c.certificates', 'cer')
            ->leftJoin('c.sections', 's')
            ->leftJoin('s.steps', 'st')
            ->where('c.id = :c_id')->setParameter('c_id', $deleteId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$course) {
            $this->router->notificationSessionFlash('noti-success', 'Course not found!');
            $this->router->redirectBack();
        }

        // delete sections and steps
        if ($course->getSections() && count($course->getSections()) > 0) {
            foreach ($course->getSections() as $section) {

                if ($section->getSteps() && count($section->getSteps()) > 0) {
                    foreach ($section->getSteps() as $step) {
                        if ($step->getType() == 'video') {
                            $formService = new FormService();
                            $formService->deleteFile($step->getVideo());
                        }

                        // delete questions
                        if ($step->getType() == 'quiz' && $step->getQuestions() && count($step->getQuestions()) > 0) {
                            foreach ($step->getQuestions() as $question) {
                                // delete answers
                                if ($question->getAnswers() && count($question->getAnswers()) > 0) {
                                    foreach ($question->getAnswers() as $answer) {
                                        $question->getAnswers()->removeElement($answer);
                                        $entityManager->remove($answer);
                                    }
                                }
                                $step->getQuestions()->removeElement($question);
                                $entityManager->remove($question);
                            }
                        }
                        $entityManager->remove($step);
                    }
                }
                $entityManager->remove($section);
            }
        }

        $entityManager->remove($course);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Course deleted successfully!');
        $this->router->redirectUsingRouteName('show-bin-course');
    }
}
