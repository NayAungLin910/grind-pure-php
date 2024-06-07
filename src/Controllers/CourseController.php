<?php

namespace Src\Controllers;

use Src\Controller;

class CourseController extends Controller {
    
    public function __construct()
    {
    }

    /**
     * Shows courses view
     */
    public function showCourses(): void
    {
        $this->render("admin/course/index");
    }
}