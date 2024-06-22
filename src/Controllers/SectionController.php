<?php

namespace Src\Controllers;

use Doctrine\ORM\NoResultException;
use Src\Controller;
use Src\Models\Course;
use Src\Models\Section;
use Src\Router;
use Src\Validators\Section\SectionValidator;

class SectionController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    /**
     * Handles post request to create section
     */
    public function postSectionCreate(): void
    {
        $sectionValidator = new SectionValidator();

        $sectionValidator->checkRequestFields(['title', 'description', 'course-id', 'priority']);

        $title = $_POST['title'];
        $description = $_POST['description'];
        $courseId = $_POST['course-id'];
        $priority = $_POST['priority'];

        $sectionValidator->titleValidate($title, 'title');
        $sectionValidator->descriptionValidate($description, 'description');
        $sectionValidator->priorityValidate($priority, 'priority');

        $sectionValidator->flashOldRequestData(compact('title', 'description', 'priority'));
        $sectionValidator->flashErrors();

        require "../config/bootstrap.php";

        try {
            $course = $entityManager->createQueryBuilder()
                ->select('c')
                ->from(Course::class, 'c')
                ->leftJoin('c.user', 'u')
                ->leftJoin('c.sections', 's')
                ->where('c.id = :id')->setParameter('id', $courseId)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) { // if no result 
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        if (count($course->getSections()) > 0) {
            foreach ($course->getSections() as $section) {

                /**
                 * increase the priority of previous section by plus one
                 * to sections which have greater or equal to priority 
                 * to the new section.
                 */
                if ($section->getPriority() >= $priority) {
                    $oldPriority = $section->getPriority();
                    $section->setPriority($oldPriority + 1);

                    $entityManager->persist($section);
                }
            }
        }

        $section = new Section();
        $section->setCourse($course);
        $section->setTitle($title);
        $section->setDescription($description);
        $section->setPriority($priority);

        $entityManager->persist($section);
        $entityManager->flush();

        $sectionValidator->resetOldRequestData();

        $this->router->notificationSessionFlash('noti-success', "Section created successfully!");
        $this->router->redirectBack();
    }

    /**
     * Handles post request to edit section
     */
    public function postSectionEdit(): void
    {
        $sectionValidator = new SectionValidator();

        $sectionValidator->checkRequestFields(['edit-section-title', 'edit-section-description', 'course-id', 'section-id', 'priority']);

        $title = $_POST['edit-section-title'];
        $description = $_POST['edit-section-description'];
        $courseId = $_POST['course-id'];
        $sectionId = $_POST['section-id'];
        $priority = $_POST['priority'];

        $sectionValidator->titleValidate($title, 'edit-section-title');
        $sectionValidator->descriptionValidate($description, 'edit-section-description');
        $sectionValidator->priorityValidate($priority, "priority");

        $sectionValidator->flashErrors();

        require "../config/bootstrap.php";

        try {
            $course = $entityManager->createQueryBuilder()
                ->select('c')
                ->from(Course::class, 'c')
                ->where('c.id = :course_id')->setParameter('course_id', $courseId)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) { // if no result 
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectBack();
        }

        try {
            $section = $entityManager->createQueryBuilder()
                ->select('s')
                ->from(Section::class, 's')
                ->andWhere('s.course_id = :course_id')->setParameter('course_id', $courseId)
                ->andWhere('s.id = :section_id')->setParameter('section_id', $sectionId)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) { // if no result 
            $this->router->notificationSessionFlash('noti-danger', 'Section not found!');
            $this->router->redirectBack();
        }

        if (count($course->getSections()) > 0) {
            foreach ($course->getSections() as $s) {

                /**
                 * increase the priority of previous section by plus one
                 * to sections which have greater or equal to priority 
                 * to the new section.
                 */
                if ($s->getPriority() >= $priority) {
                    $oldPriority = $section->getPriority();
                    $s->setPriority($oldPriority + 1);

                    $entityManager->persist($s);
                }
            }
        }

        $section->setTitle($title);
        $section->setDescription($description);
        $section->setPriority($priority);

        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', "Section updated successfully!");

        $singleCourseRoute = $this->router->getRouteUsingRouteName('show-single-course');

        $this->router->notificationSessionFlash("noti-success", "Section updated successfully!");
        $this->router->redirectTo($singleCourseRoute . "?title=" . $course->getTitle());
    }
}
