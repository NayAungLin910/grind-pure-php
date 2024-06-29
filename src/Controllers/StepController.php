<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Section;
use Src\Models\Step;
use Src\Router;
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
            'readingContext' => $readingContext,
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

        $entityManager->remove($step);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Step deleted successfully!');
        $this->router->redirectBack();
    }
}
