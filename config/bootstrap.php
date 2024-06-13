<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require '../vendor/autoload.php';

$paths = [
    "../src/Models"
];

$isDevMode = true;
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

// Database configuration parameters
$dbConfig = array(
    'dbname' => "grind_database",
    'user' => "root",
    'password' => "password",
    'host' => "127.0.0.1",
    'driver' => 'pdo_mysql',
);

$connection = DriverManager::getConnection($dbConfig, $config);

$entityManager = new EntityManager($connection, $config);

$con = $entityManager->getConnection();
$con->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
