<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Course;
use Src\Models\Product;
use Src\Models\Tag;
use Src\Router;

class UserController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    public function index(): void
    {
        require "../config/bootstrap.php";

        $phpTag = $entityManager->createQueryBuilder()
            ->select('pt')
            ->from(Tag::class, 'pt')
            ->where('pt.name = :name')->setParameter('name', 'PHP')
            ->getQuery()
            ->getOneOrNullResult();

        $javaTag = $entityManager->createQueryBuilder()
            ->select('jt')
            ->from(Tag::class, 'jt')
            ->where('jt.name = :name')->setParameter('name', 'Java')
            ->getQuery()
            ->getOneOrNullResult();

        $phpCourses = $entityManager->createQueryBuilder()
            ->select('ph')
            ->from(Course::class, 'ph')
            ->leftJoin('ph.tags', 't')
            ->where('t.id = :t_id')->setParameter('t_id', $phpTag->getId())
            ->getQuery()
            ->getResult();

        $javaCourses = $entityManager->createQueryBuilder()
            ->select('jc')
            ->from(Course::class, 'jc')
            ->leftJoin('jc.tags', 't')
            ->where('t.id = :t_id')->setParameter('t_id', $javaTag->getId())
            ->getQuery()
            ->getResult();

        $this->render('user/index', compact('phpCourses', 'javaCourses'));
    }
}
