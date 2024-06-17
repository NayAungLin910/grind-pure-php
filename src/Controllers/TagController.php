<?php

namespace Src\Controllers;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Src\Controller;
use Src\Models\Tag;
use Src\Models\User;
use Src\Router;
use Src\Validators\FormValidator;
use Src\Validators\Tag\TagValidator;

class TagController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    /**
     * Show tag
     */
    public function showTag(): void
    {
        require "../config/bootstrap.php";

        $name = isset($_GET['name']) ? $_GET['name'] : "";

        $pageSize = isset($_GET['page-size']) && $_GET['page-size'] > 0 ? $_GET['page-size'] : 10;
        $page =  isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
        $created_by_me = isset($_GET['created_by_me']) ? $_GET['created_by_me'] : null;
        $sortByOldest = isset($_GET['oldest']) ? $_GET['oldest'] : null;


        $paginationDql = $entityManager->createQueryBuilder()
            ->select('t')
            ->from(Tag::class, 't')
            ->join('t.user', 'u');

        if ($name !== "") $paginationDql->where('t.name LIKE :name')->setParameter('name', "%$name%");

        if ($created_by_me) $paginationDql->where('u.id = :id')->setParameter('id', $_SESSION['auth']['id']);

        if ($sortByOldest) {
            $paginationDql = $paginationDql->orderBy('t.created_at', 'ASC');
        } else {
            $paginationDql = $paginationDql->orderBy('t.created_at', 'DESC');
        }

        $paginationDql->andWhere('t.deleted = false');
        $paginationDql->setFirstResult(($page - 1) * $pageSize);
        $paginationDql->setMaxResults($pageSize);

        $paginator = new Paginator($paginationDql);

        $tags = [];

        foreach ($paginator as $tag) {
            $tags[] = $tag;
        }

        $totalItems = count($paginator); // all the rows of the table filtered from paginationQuery
        $totalPages = ceil($totalItems / $pageSize);

        $formValidator = new FormValidator();
        $formValidator->flashOldRequestData(compact('name', 'created_by_me', 'sortByOldest'));

        $this->render('admin/tag/index', compact('tags', 'pageSize', 'page', 'totalItems', 'totalPages'));
    }

    /**
     * Dleete tag
     */
    public function postDeleteTag(): void
    {
        if (!isset($_POST['delete-id'])) {
            $this->router->notificationSessionFlash('noti-danger', 'Id not found!');
            $this->router->redirectUsingRouteName('show-tag');
        }

        require "../config/bootstrap.php";

        $tag = $entityManager->getRepository(Tag::class)->findOneBy([
            'id' => $_POST['delete-id']
        ]);

        $tag->setDeleted(true);

        $entityManager->persist($tag);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', "Tag moved to bin successfully!");
        $this->router->redirectUsingRouteName("show-tag");
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

        $this->router->notificationSessionFlash('noti-success', 'Tag created successfully!');
        $this->router->redirectUsingRouteName('show-tag-create');
    }

    /**
     * Show edit page for tag
     */
    public function showEditTag(): void
    {
        if (!isset($_GET['update-id'])) {
            $this->router->notificationSessionFlash('noti-danger', 'Id not found!');
            $this->router->redirectUsingRouteName('show-tag');
        }

        require "../config/bootstrap.php";

        $tag = $entityManager->getRepository(Tag::class)->findOneBy([
            'id' => $_GET['update-id']
        ]);

        if (!$tag) {
            $this->router->notificationSessionFlash('noti-danger', 'Tag not found!');
            $this->router->redirectUsingRouteName('show-tag');
        }

        $this->render('/admin/tag/edit', compact('tag'));
    }

    /**
     * Handles edit tag post request
     */
    public function postEditTag(): void
    {
        if (!isset($_POST['update-id'])) {
            $this->router->notificationSessionFlash('noti-danger', 'Id not found!');
            $this->router->redirectUsingRouteName('show-tag');
        }

        require "../config/bootstrap.php";

        $tag = $entityManager->getRepository(Tag::class)->findOneBy([
            'id' => $_POST['update-id']
        ]);

        if (!$tag) {
            $this->router->notificationSessionFlash('noti-danger', 'Tag not found!');
            $this->router->redirectUsingRouteName('show-tag');
        }

        $name = $_POST['name'];

        $tagValidator = new TagValidator();

        $tagValidator->nameValidate($name, 'name', $tag->getId());
        $tagValidator->checkRequestFields(['name']);
        $tagValidator->flashErrors();

        $tag->setName($name);

        $entityManager->persist($tag);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Tag updated successfully!');
        $this->router->redirectUsingRouteName('show-tag');
    }
}
