<?php

namespace Src;

use Exception;

class Controller
{

    /**
     * Render the given view
     */
    public function render(string $view, array $data = []): void
    {
        if (!empty($data)) {
            extract($data);
        }

        include "routes.php";

        include "Views/$view.php";
    }
}
