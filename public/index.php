<?php

require '../vendor/autoload.php';

require '../src/routes.php';

$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($uri); 