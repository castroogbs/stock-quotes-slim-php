<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use App\Service\UserService;
use App\Service\StockQuoteResearchService;
use App\Service\AuthService;

use Symfony\Component\Mailer\Mailer; 
use Symfony\Component\Mailer\Transport;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([

        'settings' => [
            'doctrine' => [
                'dev_mode' => $_ENV['ENV'] === 'dev',
                'cache_dir' => __DIR__ . '/../var/doctrine',
                'metadata_dirs' => [__DIR__ . '/../src/Model'],
                'connection' => [
                    'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
                    'host' => $_ENV['DB_HOST'],
                    'port' => $_ENV['DB_PORT'] ?? 3306,
                    'dbname' => $_ENV['DB_NAME'],
                    'user' => $_ENV['DB_USER'],
                    'password' => $_ENV['DB_PASS'],
                    'charset' => $_ENV['DB_CHARSET'] ?? 'utf-8'
                ]
            ]
        ],

        Mailer::class => function(ContainerInterface $c) {
            $transport = Transport::fromDsn($_ENV['MAILER_DSN']); 
            return new Mailer($transport);
        },

        EntityManager::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');

            $cache = $settings['doctrine']['dev_mode'] ?
                DoctrineProvider::wrap(new ArrayAdapter()) :
                DoctrineProvider::wrap(new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']));

            $config = Setup::createAttributeMetadataConfiguration(
                $settings['doctrine']['metadata_dirs'],
                $settings['doctrine']['dev_mode'],
                null,
                $cache
            );

            return EntityManager::create($settings['doctrine']['connection'], $config);
        },
        

        UserService::class => function(ContainerInterface $c) {
            return new UserService($c->get(EntityManager::class));
        },
        
        StockQuoteResearchService::class => function(ContainerInterface $c) {
            return new StockQuoteResearchService($c->get(EntityManager::class));
        },
        
        AuthService::class => function(ContainerInterface $c) {
            return new AuthService($c->get(EntityManager::class));
        },


    ]);

};
