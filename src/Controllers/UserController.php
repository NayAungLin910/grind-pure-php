<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Course;
use Src\Models\Product;

class UserController extends Controller
{
    public function index(): void
    {
        
        require_once "../config/bootstrap.php";
        $post = new Product();
        $course = new Course();
        $post->setTitle('hello Post');

        echo "Hello";
        var_dump($post);
    
        $entityManager->persist($post);
        $entityManager->flush();

        $this->render('user/index');
    }
}
