<?php

namespace Src\Controllers;

use FPDF;
use Src\Controller;
use Src\Models\Answer;
use Src\Models\Course;
use Src\Models\Enrollment;
use Src\Models\Question;
use Src\Models\Section;
use Src\Models\Step;
use Src\Models\User;
use Src\Router;
use Src\Services\CourseService;
use Src\Services\FormService;
use Src\Validators\Step\StepValidator;

class StepController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    /**
     * Show step creation page
     */
    public function showStepCreate()
    {
        $stepValidator = new StepValidator();

        $stepValidator->checkRequestFields(['section-id']);

        $stepId = $_GET['section-id'];

        require "../config/bootstrap.php";

        $section = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Section::class, 's')
            ->leftJoin('s.course', 'c')
            ->leftJoin('s.steps', 'st')
            ->where('s.id = :section_id')->setParameter('section_id', $stepId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$section) {
            $this->router->notificationSessionFlash('noti-danger', 'Section not found!');
            $this->router->redirectBack();
        }

        $this->render("/admin/step/create",  compact('section'));
    }

    /**
     * Handle post request to create a new step 
     */
    public function postStepCreate(): void
    {
        $stepValidator = new StepValidator();

        $stepValidator->checkRequestFields(['section-id', 'title', 'description', 'priority', 'type']);

        $sectionId = $_POST['section-id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $priority = $_POST['priority'];
        $type = $_POST['type'];

        $video = $_FILES['video'];
        $readingContext = $_POST['reading-context'];

        $stepValidator->titleValidate($title, 'title');
        $stepValidator->descriptionValidate($description, 'description');
        $stepValidator->priorityValidate($priority, 'priority');
        if ($type == 'video') $stepValidator->videoValidate($video, 'video');
        if ($type == 'reading') $stepValidator->readingContextValidate($readingContext, 'reading context');

        $stepValidator->flashOldRequestData([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'reading context' => $readingContext,
        ]);
        $flashRoute = $this->router->getRouteUsingRouteName('show-step-create') . "?section-id=$sectionId&type=$type";
        $stepValidator->flashErrors($flashRoute);

        require("../config/bootstrap.php");

        $step = new Step();
        $step->setTitle($title);
        $step->setDescription($description);
        $step->setPriority($priority);
        $step->setType($type);

        if ($type === 'video') { // if video step, upload video file
            $formService = new FormService();
            $videoDir = $formService->uploadFiles($video, '/videos', 'video');
            $step->setVideo($videoDir);
        } elseif ($type === 'reading') { // if reading step, save reading context
            $step->setReadingContent($readingContext);
        }

        require '../config/bootstrap.php';

        $section = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Section::class, 's')
            ->where('s.id = :id')->setParameter('id', $sectionId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$section) {
            $this->router->notificationSessionFlash('noti-danger', 'Section not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        if (count($section->getSteps()) > 0) {
            foreach ($section->getSteps() as $s) {

                /**
                 * increase the priority of previous section by plus one
                 * to sections which have greater or equal to priority 
                 * to the new section.
                 */
                if ($s->getPriority() >= $priority) {
                    $oldPriority = $s->getPriority();
                    $s->setPriority($oldPriority + 1);

                    $entityManager->persist($s);
                }
            }
        }

        $step->setSection($section); // set many-to-one entity
        $section->getSteps()->add($step); // set one-to-many entity

        $entityManager->persist($step);
        $entityManager->flush();

        $stepValidator->resetOldRequestData();

        $this->router->notificationSessionFlash('noti-success', 'New step created successfully!');
        $route = $this->router->getRouteUsingRouteName('show-step-create') . "?section-id=" . $sectionId . "&type=video";
        $this->router->redirectTo($route);
    }

    /**
     * Handle post request to delete a step
     */
    public function postStepDelete(): void
    {
        $stepValidator = new StepValidator();
        $stepValidator->checkRequestFields(['delete-id']);

        $deleteId = $_POST['delete-id'];

        require "../config/bootstrap.php";

        $step = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Step::class, 's')
            ->leftJoin('s.questions', 'q')
            ->leftJoin('q.answers', 'a')
            ->where('s.id = :step_id')->setParameter('step_id', $deleteId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$step) {
            $this->router->notificationSessionFlash('noti-danger', 'Step not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        if ($step->getType() == 'video') {
            $formService = new FormService();
            $formService->deleteFile($step->getVideo());
        }


        if ($step->getType() == 'quiz' && $step->getQuestions() && count($step->getQuestions()) > 0) {

            // delete questions
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
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Step deleted successfully!');
        $this->router->redirectBack();
    }

    /**
     * Shows step edit page
     */
    public function showStepEdit(): void
    {
        $stepValidator = new StepValidator();
        $stepValidator->checkRequestFields(['edit-id']);

        $stepId = $_GET['edit-id'];
        $questionEditId = isset($_GET['question-edit']) ? $_GET['question-edit'] : '';
        $questionAnswerId = isset($_GET['question-add-answer']) ? $_GET['question-add-answer'] : '';
        $answerEditId = isset($_GET['answer-edit-id']) ? $_GET['answer-edit-id'] : '';

        require '../config/bootstrap.php';

        $step = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Step::class, 's')
            ->leftJoin('s.section', 'se')
            ->leftJoin('se.course', 'c')
            ->leftJoin('s.questions', 'q')
            ->leftJoin('q.answers', 'a')
            ->where('s.id = :step_id')->setParameter('step_id', $stepId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$step) {
            $this->router->notificationSessionFlash('noti-danger', 'Step not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $quesitonEdit = null;

        if ($questionEditId) {
            $questionEdit = $entityManager->createQueryBuilder()
                ->select('q')
                ->from(Question::class, 'q')
                ->where('q.id = :q_id')->setParameter('q_id', $questionEditId)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$questionEdit) {
                $this->router->notificationSessionFlash('noti-danger', 'Question not found!');
                $this->router->redirectUsingRouteName('show-course');
            }
        }

        $questionAnswer = null;

        if ($questionAnswerId) {
            $questionAnswer = $entityManager->createQueryBuilder()
                ->select('q')
                ->from(Question::class, 'q')
                ->where('q.id = :q_id')->setParameter('q_id', $questionAnswerId)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$questionAnswer) {
                $this->router->notificationSessionFlash('noti-danger', 'Question not found!');
                $this->router->redirectUsingRouteName('show-course');
            }
        }

        $answerEdit = null;

        if ($answerEditId) {
            $answerEdit = $entityManager->createQueryBuilder()
                ->select('a')
                ->from(Answer::class, 'a')
                ->leftJoin('a.question', 'q')
                ->where('a.id = :answer_id')->setParameter('answer_id', $answerEditId)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$answerEdit) {
                $this->router->notificationSessionFlash('noti-danger', 'Answer not found!');
                $this->router->redirectUsingRouteName('show-course');
            }
        }

        $this->render('/admin/step/edit', compact('step', 'questionEdit', 'questionAnswer', 'answerEdit'));
    }

    /**
     * Handle post request to update step
     */
    public function postStepEdit(): void
    {
        $stepValidator = new StepValidator();

        $stepValidator->checkRequestFields(['step-id', 'title', 'description', 'priority', 'type']);

        $stepId = $_POST['step-id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $priority = $_POST['priority'];
        $type = $_POST['type'];

        $video = $type === 'video' ? $_FILES['video'] : null;
        $readingContext = $type === 'reading' ? $_POST['reading-context'] : null;

        $stepValidator->titleValidate($title, 'title');
        $stepValidator->descriptionValidate($description, 'description');
        $stepValidator->priorityValidate($priority, 'priority');
        if ($video && $video['name'] !== '') $stepValidator->videoValidate($video, 'video');
        if ($readingContext) $stepValidator->readingContextValidate($readingContext, 'reading context');

        $stepValidator->flashOldRequestData([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'reading context' => $readingContext,
        ]);
        $stepValidator->flashErrors();

        require "../config/bootstrap.php";

        $step = $entityManager->getRepository(Step::class)->findOneBy([
            'id' => $stepId
        ]);

        $step = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Step::class, 's')
            ->where('s.id = :id')->setParameter('id', $stepId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$step) {
            $this->router->notificationSessionFlash('noti-danger', 'Step not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $step->setTitle($title);
        $step->setDescription($description);
        $step->setPriority($priority);

        if ($type === 'video' && $video['name'] !== '') { // if video step and there is a new video uploaded

            // delete old video
            $formService = new FormService();
            $formService->deleteFile($step->getVideo());

            $videoDir = $formService->uploadFiles($video, '/videos', 'video');
            $step->setVideo($videoDir);
        } elseif ($type === 'reading') { // if reading step, save reading context
            $step->setReadingContent($readingContext);
        }

        $section = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Section::class, 's')
            ->where('s.id = :id')->setParameter('id', $step->getSection()->getId())
            ->getQuery()
            ->getOneOrNullResult();

        if (!$section) {
            $this->router->notificationSessionFlash('noti-danger', 'Section not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        if ($priority > $step->getPriority() && count($section->getSteps()) > 0) {
            foreach ($section->getSections() as $s) {
                /**
                 * decrease the priority of previous section by plus one
                 * to sections which have equal to or lesser priority 
                 * to the current section.
                 */
                if ($s->getPriority() <= $priority && $s->getId() !== $step->getId()) {
                    $oldPriority = $s->getPriority();
                    $s->setPriority($oldPriority - 1);
                    $entityManager->persist($s);
                }
            }
        } elseif ($priority < $step->getPriority() && count($section->getSteps()) > 0) {
            foreach ($section->getSteps() as $s) {
                /**
                 * increase the priority of previous section by plus one
                 * to sections which have greater or equal to priority 
                 * to the current section.
                 */
                if ($s->getPriority() >= $priority && $s->getId() !== $step->getId()) {
                    $oldPriority = $s->getPriority();
                    $s->setPriority($oldPriority + 1);
                    $entityManager->persist($s);
                }
            }
        }

        $entityManager->flush();
        $stepValidator->resetOldRequestData();

        $this->router->notificationSessionFlash('noti-success', 'Step updated successfully!');
        $this->router->redirectBack();
    }

    /**
     * Handles post request to complete a step
     */
    public function postStepComplete(): void
    {
        $stepValidator = new StepValidator();

        $stepValidator->checkRequestFields(['current-step-id']);

        $stepId = $_POST['current-step-id'];

        require "../config/bootstrap.php";

        $step = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Step::class, 's')
            ->leftJoin('s.users', 'u')
            ->leftJoin('s.section', 'se')
            ->leftJoin('se.course', 'c')
            ->where('s.id = :s_id')->setParameter('s_id', $stepId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$step) {
            $this->router->notificationSessionFlash('noti-danger', 'Step not found!');
            $this->router->redirectUsingRouteName('show-public-course');
        }

        $user = $entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->leftJoin('u.completedSteps', 'st')
            ->where('u.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id'])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            $this->router->notificationSessionFlash('noti-danger', 'User not found!');
            $this->router->redirectUsingRouteName('show-public-course');
        }

        $contains = false;

        if ($user->getCompletedSteps() && count($user->getCompletedSteps()) > 0) {
            foreach ($user->getCompletedSteps() as $st) {
                if ($st->getId() === $step->getId()) {
                    $contains = true;
                }
            }
        }

        if (!$contains) {
            $user->getCompletedSteps()->add($step);
            $step->getUsers()->add($user);
        }

        $entityManager->flush();

        $courseService = new CourseService();
        $courseCompleted = $courseService->checkCourseCompleted($step->getSection()->getCourse());

        if ($courseCompleted) {
            $enrollment = $entityManager->createQueryBuilder()
                ->select('e')
                ->from(Enrollment::class, 'e')
                ->leftJoin('e.course', 'c')
                ->leftJoin('e.user', 'u')
                ->where("c.id = :course_id")->setParameter('course_id', $step->getSection()->getCourse()->getId())
                ->andWhere('u.id = :user_id')->setParameter('user_id', $_SESSION['auth']['id'])
                ->getQuery()
                ->getOneOrNullResult();

            if (!$enrollment) {
                $this->router->notificationSessionFlash('noti-danger', 'Enrollment not found!');
                $this->router->redirectUsingRouteName('show-public-course');
            }

            $enrollment->setStatus('completed');
            $entityManager->flush();
        }

        if (!$contains) {
            $this->router->notificationSessionFlash('noti-success', 'Step completed!');
        } else {
            $this->router->notificationSessionFlash('noti-success', 'Step already completed!');
        }
        $route = $this->router->getRouteUsingRouteName('show-public-spec-course') . "?title=" . $step->getSection()->getCourse()->getTitle() . "&current-step=" . $step->getId();
        $this->router->redirectTo($route);
    }

    /**
     * Handles post request to submit answers
     */
    public function postQuizAnswer(): void
    {

        $stepValidator = new StepValidator();
        $stepValidator->checkRequestFields(['step-id']);
        $stepId = $_POST['step-id'];

        require "../config/bootstrap.php";

        $step = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Step::class, 's')
            ->leftJoin('s.questions', 'q')
            ->leftJoin('s.section', 'sec')
            ->leftJoin('sec.course', 'c')
            ->leftJoin('s.users', 'u')
            ->leftJoin('q.answers', 'a')
            ->where('s.id = :s_id')->setParameter('s_id', $stepId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$step) {
            $this->router->notificationSessionFlash('noti-danger', 'Step not found!');
            $this->router->redirectUsingRouteName('show-public-course');
        }

        $user = $entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->leftJoin('u.completedSteps', 'st')
            ->where('u.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id'])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            $this->router->notificationSessionFlash('noti-danger', 'User not found!');
            $this->router->redirectUsingRouteName('show-public-course');
        }

        $questionIds = [];

        if ($step->getQuestions() && count($step->getQuestions()) > 0) {
            foreach ($step->getQuestions() as $q) {
                $questionIds[] = $q->getId();
            }
        }

        $correctAnswerCount = 0;

        foreach ($questionIds as $qId) {
            if (isset($_POST["question-$qId"])) {
                $answerChosen = $_POST["question-$qId"];

                $answer = $entityManager->createQueryBuilder()
                    ->select('a')
                    ->from(Answer::class, 'a')
                    ->where("a.id = :a_id")->setParameter('a_id', $answerChosen)
                    ->andWhere('a.question_id = :q_id')->setParameter('q_id', $qId)
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!$answer) {
                    $this->router->notificationSessionFlash('noti-danger', 'Invalid answer');
                    $this->router->redirectUsingRouteName('show-public-course');
                }

                if ($answer->getCorrect()) { // if correct answer
                    $correctAnswerCount++;
                    $stepValidator->addError("question-$qId", $answer->getExplanation());
                } else {
                    $stepValidator->addError("question-$qId-error", $answer->getExplanation());
                }
            } else {
                $stepValidator->addError("question-error-$qId", 'Please select an answer');
            }
        }

        $correctPercentage = ($correctAnswerCount / count($step->getQuestions())) * 100;

        if ($correctPercentage >= 80) {

            $contains = false;

            if ($user->getCompletedSteps() && count($user->getCompletedSteps()) > 0) {
                foreach ($user->getCompletedSteps() as $st) {
                    if ($st->getId() === $step->getId()) {
                        $contains = true;
                    }
                }
            }

            if (!$contains) {
                $user->getCompletedSteps()->add($step);
                $step->getUsers()->add($user);
            }

            $entityManager->flush();

            $courseService = new CourseService();
            $courseCompleted = $courseService->checkCourseCompleted($step->getSection()->getCourse());

            if ($courseCompleted) {
                $enrollment = $entityManager->createQueryBuilder()
                    ->select('e')
                    ->from(Enrollment::class, 'e')
                    ->leftJoin('e.course', 'c')
                    ->leftJoin('e.user', 'u')
                    ->where("c.id = :course_id")->setParameter('course_id', $step->getSection()->getCourse()->getId())
                    ->andWhere('u.id = :user_id')->setParameter('user_id', $_SESSION['auth']['id'])
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!$enrollment) {
                    $this->router->notificationSessionFlash('noti-danger', 'Enrollment not found!');
                    $this->router->redirectUsingRouteName('show-public-course');
                }

                $enrollment->setStatus('completed');
                $entityManager->flush();
            }

            $stepValidator->addError("result", "Congratulations! You have guessed the 80 percent of the answers correctly and passed the quiz.");
        } else {
            $stepValidator->addError("result-error", "You did not guess the 80 percent of the answers correctly, please try again!");
        }

        $stepValidator->flashErrors();
    }

    /**
     * Let the user download the certificate
     * of completed course
     */
    public function getDownloadCert(): void
    {
        $stepValidator = new StepValidator();
        $stepValidator->checkRequestFields(['course-id']);

        $courseId = $_GET['course-id'];

        require "../config/bootstrap.php";

        $course = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->leftJoin('c.tags', 't')
            ->leftJoin('c.enrollments', 'e')
            ->leftJoin('e.user', 'u')
            ->andWhere('e.user_id = :eu_id')->setParameter('eu_id', $_SESSION['auth']['id'])
            ->andWhere('c.id = :c_id')->setParameter('c_id', $courseId)
            ->andWhere("e.status = 'completed'")
            ->getQuery()
            ->getOneOrNullResult();

        if (!$course) {
            $this->router->notificationSessionFlash('noti-danger', 'Course not found!');
            $this->router->redirectUsingRouteName('profile');
        }

        // produce a certificate 
        $backgroundCert = imagecreatefromjpeg('./default/certificate/background_cert.jpg');
        $font = "./default/font/GloriousChristmas-BLWWB.ttf";
        $color = imagecolorallocate($backgroundCert, 19, 21, 22);
        $imageName = uniqid() . ".jpg";
        imagettftext($backgroundCert, 45, 0, 470, 800, $color, $font, $_SESSION['auth']['name'] . " has completed the course " . $course->getTitle());
        imagejpeg($backgroundCert, "./images/certificates/" . $imageName);

        $route = '/images/certificates/' . $imageName;

        $this->router->redirectTo($route);
    }
}
