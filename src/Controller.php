<?php

namespace Src;

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

        include "../src/routes.php";

        include "../src/Views/utilities/ViewUtilities.php";

        include "../src/Views/$view.php";
    }
}