<?php

namespace Src\Controllers;

use Src\Controller;

class UserController extends Controller
{
    public function index(): void
    {
        $this->render('user/index');
    }
}
