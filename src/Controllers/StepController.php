<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Section;
use Src\Models\Step;
use Src\Router;
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
        $step->setSectionId($sectionId);
        $step->setTitle($title);
        $step->setDescription($description);

        if ($step) {
        }

        var_dump('success');
        die();
    }
}
