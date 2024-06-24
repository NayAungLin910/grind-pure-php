<?php

namespace Src\Controllers\Bin;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Src\Controller;
use Src\Models\Tag;
use Src\Router;
use Src\Validators\FormValidator;

class TagController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    /**
     * Show the tags deleted
     */
    public function showBinTag(): void
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

        $paginationDql->andWhere('t.deleted = true');
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

        $this->render('admin/bin/tag/index', compact('tags', 'pageSize', 'page', 'totalItems', 'totalPages'));
    }

    /**
     * Delete the tag permanantly
     */
    public function postDeleteBinTag(): void
    {
        if (!isset($_POST['delete-id'])) {
            $this->router->notificationSessionFlash('noti-danger', 'Id not found!');
            $this->router->redirectUsingRouteName('show-bin-tag');
        }

        require "../config/bootstrap.php";

        $tag = $entityManager->getRepository(Tag::class)->findOneBy([
            'id' => $_POST['delete-id']
        ]);

        if (!$tag) {
            $this->router->notificationSessionFlash('noti-danger', 'Tag not found!');
            $this->router->redirectUsingRouteName('show-bin-tag');
        }

        foreach ($tag->getCourses() as $c) { // removes old many-to-many relationship of tags
            $c->getTags()->removeElement($tag);
            $tag->getCourses()->removeElement($c);
        }

        $entityManager->flush();

        $entityManager->remove($tag);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Tag deleted successfully!');
        $this->router->redirectUsingRouteName('show-bin-tag');
    }

    /**
     * Recover the tag from bin
     */
    public function postBinTagRecover(): void
    {
        if (!isset($_POST['recover-id'])) {
            $this->router->notificationSessionFlash('noti-danger', 'Id not found!');
            $this->router->redirectUsingRouteName('show-bin-tag');
        }

        require "../config/bootstrap.php";

        $tag = $entityManager->getRepository(Tag::class)->findOneBy([
            'id' => $_POST['recover-id']
        ]);

        $tag->setDeleted(false);

        $entityManager->persist($tag);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', "Tag recovered successfully!");
        $this->router->redirectUsingRouteName("show-bin-tag");
    }
}
