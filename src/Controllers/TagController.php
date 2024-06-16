<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Tag;
use Src\Models\User;
use Src\Router;
use Src\Validators\Tag\TagValidator;

class TagController extends Controller
{
    public function __construct(private $router = new Router())
    {
        
    }

    /**
     * Renders the view of tags
     */
    public function showCreateTag(): void
    {
        $this->render('admin/tag/create');
    }

    /**
     * Handles post request to create a tag
     */
    public function postCreateTag(): void
    {
        $tagValidator = new TagValidator();

        $tagValidator->checkRequestFields(['name']);

        $name = $_POST['name'];

        $tagValidator->nameValidate($name, 'name');

        $tagValidator->flashOldRequestData([
            'name' => $name,
        ]);
        $tagValidator->flashErrors();

        require "../config/bootstrap.php";

        $user = $entityManager->getRepository(User::class)->findOneById($_SESSION['auth']['id']);

        if (!$user) {
            $tagValidator->addError("user-not-found", "User not found!");
            $tagValidator->flashErrors();
        }

        $tag = new Tag;
        $tag->setUser($user);
        $tag->setName($name);

        $entityManager->persist($tag);
        $entityManager->flush();

        $tagValidator->resetOldRequestData();   

        $this->router->redirectUsingRouteName('show-tag-create');
    }
}
