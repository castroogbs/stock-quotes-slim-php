<?php

require 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$config = new PhpFile('migrations.php');
$params = [
    'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'] ?? 3306,
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf-8'
];

$entityManager = EntityManager::create(
    $params,
    Setup::createAttributeMetadataConfiguration([__DIR__.'/src/Model'])
);

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));