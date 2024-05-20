<?php

namespace Src\Controllers;

use Src\Controller;

class AuthConroller extends Controller {
    public function showRegister(): void {
        
        $this->render('auth/register');
    }

    public function registerPost(): void {
        return;
    }
}