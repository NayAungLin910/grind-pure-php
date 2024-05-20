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

        include "Views/$view.php";
    }
}
