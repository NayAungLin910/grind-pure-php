<?php

namespace Src\Controllers;

use Src\Controller;

class StepController extends Controller
{
    public function postStepCreate(): void
    {
        var_dump($_POST);
    }
}
