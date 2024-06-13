<?php

namespace Src\Controllers;

use Src\Controller;
use Src\Models\Course;
use Src\Models\Product;

class UserController extends Controller
{
    public function index(): void
    {
        $this->render('user/index');
    }
}
