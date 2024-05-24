<?php

use Dotenv\Dotenv;

require '../vendor/autoload.php';

require '../src/routes.php';

$dontenv = Dotenv::createImmutable('../config');
$dontenv->load();

$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($uri); 