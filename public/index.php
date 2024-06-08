<?php

use Dotenv\Dotenv;

require '../vendor/autoload.php';

session_start();

require '../src/routes.php';

$dontenv = Dotenv::createImmutable('../config');
$dontenv->load();

$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($uri); 