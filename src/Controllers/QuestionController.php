<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Question;
use Src\Models\Step;
use Src\Router;
use Src\Validators\Question\QuestionValidator;

class QuestionController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    // Handles post request to edit questio
    public function postQuestionEdit(): void
    {
        $questionValidator = new QuestionValidator();

        $questionValidator->checkRequestFields(['step-id', 'question', 'question-id']);

        $stepId = $_POST['step-id'];
        $description = $_POST['question'];
        $questionId = $_POST['question-id'];

        $questionValidator->descriptionValidate($description, 'question');
        $questionValidator->stepIdValidate($stepId, 'step-id');

        $questionValidator->flashOldRequestData([
            'question' => $description
        ]);

        $questionValidator->flashErrors();

        require "../config/bootstrap.php";

        $step = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Step::class, 's')
            ->where('s.id = :id')->setParameter('id', $stepId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$step) {
            $this->router->notificationSessionFlash('noti-danger', 'Section not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $question = $entityManager->createQueryBuilder()
            ->select('q')
            ->from(Question::class, 'q')
            ->where('q.id = :id')->setParameter('id', $questionId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$question) {
            $this->router->notificationSessionFlash('noti-danger', 'Question not found!');
            $this->router->redirectTo('show-course');
        }

        $question->setDescription($description);
        $entityManager->flush();

        $questionValidator->resetOldRequestData();

        $this->router->notificationSessionFlash('noti-success', 'Question updated successfully!');
        $route = $this->router->getRouteUsingRouteName('show-step-edit') . "?edit-id=" . $step->getId();
        $this->router->redirectTo($route);
    }

    // Handles post request to create question
    public function postQuestionCreate(): void
    {
        $questionValidator = new QuestionValidator();

        $questionValidator->checkRequestFields(['step-id', 'question']);

        $stepId = $_POST['step-id'];
        $question = $_POST['question'];

        $questionValidator->descriptionValidate($question, 'question');
        $questionValidator->stepIdValidate($stepId, 'step-id');

        $questionValidator->flashOldRequestData([
            'question' => $question
        ]);
        $questionValidator->flashErrors();

        require "../config/bootstrap.php";

        $step = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Step::class, 's')
            ->where('s.id = :id')->setParameter('id', $stepId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$step) {
            $this->router->notificationSessionFlash('noti-danger', 'Section not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $newQuestion = new Question();
        $newQuestion->setDescription($question);
        $newQuestion->setStep($step);

        $entityManager->persist($newQuestion);
        $entityManager->flush();

        $questionValidator->resetOldRequestData();

        $this->router->notificationSessionFlash('noti-success', 'Question created successfully!');
        $route = $this->router->getRouteUsingRouteName('show-step-edit') . "?edit-id=" . $step->getId();
        $this->router->redirectTo($route);
    }

    /**
     * Handles post request to delete question
     */
    public function postQuestionDelete(): void
    {
        $questionValidator = new QuestionValidator();
        $questionValidator->checkRequestFields(['question-delete-id']);

        $questionId = $_POST['question-delete-id'];

        $questionValidator->checkInteger($questionId, 'question-delete-id');

        $questionValidator->flashErrors();

        require "../config/bootstrap.php";

        $question = $entityManager->createQueryBuilder()
            ->select('q')
            ->from(Question::class, 'q')
            ->leftJoin('q.answers', 'a')
            ->leftJoin('q.step', 's')
            ->where('q.id = :q_id')->setParameter('q_id', $questionId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$question) {
            $this->router->notificationSessionFlash('noti-danger', 'Question not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        // delete each answer of the question
        if ($question->getAnswers() && count($question->getAnswers()) > 0) {
            foreach ($question->getAnswers() as $answer) {
                $question->getAnswers()->removeElement($answer);
                $entityManager->remove($answer);
            }
        }

        $stepId = $question->getStep()->getId();

        $entityManager->remove($question);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Answer deleted successfully!');
        $route = $this->router->getRouteUsingRouteName('show-step-edit') . "?edit-id=" . $stepId;
        $this->router->redirectTo($route);
    }
}
