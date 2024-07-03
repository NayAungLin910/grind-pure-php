<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Answer;
use Src\Models\Question;
use Src\Router;
use Src\Validators\Answer\AnswerValidator;

class AnswerController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    /**
     * Handle post request to edit a 
     */
    public function postAnswerEdit(): void
    {
        $answerValidator = new AnswerValidator();
        $answerValidator->checkRequestFields(['answer-id', 'answer', 'explanation']);

        $answerId = $_POST['answer-id'];
        $answerDes = $_POST['answer'];
        $explanation = $_POST['explanation'];
        $correct = isset($_POST['correct']) ? true : false;

        $answerValidator->checkInteger($answerId, 'answer-id');
        $answerValidator->descriptionValidate($answerDes, 'answer');
        $answerValidator->explanationValidate($explanation, 'explanation');

        $answerValidator->flashErrors();

        require "../config/bootstrap.php";

        $answer = $entityManager->createQueryBuilder()
            ->select('a')
            ->from(Answer::class, 'a')
            ->leftJoin('a.question', 'q')
            ->leftJoin('q.step', 's')
            ->where('a.id = :a_id')->setParameter('a_id', $answerId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$answer) {
            $this->router->notificationSessionFlash('noti-danger', 'Answer not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $answer->setDescription($answerDes);
        $answer->setExplanation($explanation);
        $answer->setCorrect($correct);

        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Answer updated successfully!');
        $route = $this->router->getRouteUsingRouteName('show-step-edit') . "?edit-id=" . $answer->getQuestion()->getStep()->getId();
        $this->router->redirectTo($route);
    }

    /**
     * Handles post request to create answer
     */
    public function postAnswerCreate(): void
    {
        $answerValidator = new AnswerValidator();
        $answerValidator->checkRequestFields(['question-id', 'answer', 'explanation']);

        $questionId = $_POST['question-id'];
        $answerDes = $_POST['answer'];
        $explanation = $_POST['explanation'];
        $correct = isset($_POST['correct']) ? true : false;

        $answerValidator->checkInteger($questionId, 'question-id');
        $answerValidator->descriptionValidate($answerDes, 'answer');
        $answerValidator->explanationValidate($explanation, 'explanation');

        $answerValidator->flashOldRequestData([
            'answer' => $answerDes,
            'explanation' => $explanation
        ]);
        $answerValidator->flashErrors();

        require "../config/bootstrap.php";

        $question = $entityManager->createQueryBuilder()
            ->select('q')
            ->from(Question::class, 'q')
            ->leftJoin('q.step', 's')
            ->where('q.id = :q_id')->setParameter('q_id', $questionId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$question) {
            $this->router->notificationSessionFlash('noti-danger', 'Section not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $answer = new Answer();
        $answer->setDescription($answerDes);
        $answer->setExplanation($explanation);
        $answer->setQuestion($question);
        if ($correct) $answer->setCorrect($correct);

        $entityManager->persist($answer);
        $entityManager->flush();

        $answerValidator->resetOldRequestData();

        $this->router->notificationSessionFlash('noti-success', 'Answer created successfully!');
        $route = $this->router->getRouteUsingRouteName('show-step-edit') . "?edit-id=" . $question->getStep()->getId();
        $this->router->redirectTo($route);
    }

    /**
     * Handle post request to delete answer
     */
    public function postAnswerDelete(): void
    {
        $answerValidator = new AnswerValidator();
        $answerValidator->checkRequestFields(['answer-delete-id']);

        $answerId = $_POST['answer-delete-id'];

        $answerValidator->checkInteger($answerId, 'answer-delete-id');

        require "../config/bootstrap.php";

        $answer = $entityManager->createQueryBuilder()
            ->select('a')
            ->from(Answer::class, 'a')
            ->leftJoin('a.question', 'q')
            ->leftJoin('q.step', 's')
            ->where('a.id = :a_id')->setParameter('a_id', $answerId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$answer) {
            $this->router->notificationSessionFlash('noti-danger', 'Answer not found!');
            $this->router->redirectUsingRouteName('show-course');
        }

        $entityManager->remove($answer);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Answer deleted successfully!');
        $route = $this->router->getRouteUsingRouteName('show-step-edit') . "?edit-id=" . $answer->getQuestion()->getStep()->getId();
        $this->router->redirectTo($route);  
    }
}
