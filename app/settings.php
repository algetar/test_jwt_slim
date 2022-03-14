<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'addContentLengthHeader' => false,
                'db' => [
                    'host'   => 'db',
                    'user'   => 'user',
                    'password'   => 'pass',
                    'dbname' => 'postgres',
                    'port' => '5432',
                ],
                'jwt' => [
                    'secret' => 'u20iC05YtvNttnSY04l1CPziGF+GSGOoaZfCEc1TfnE=',
                    'encryption_key' => 'u20iC05YtvNttnSY04l1CPziGF+GSGOoaZfCEc1TfnE=',
                    'private_key' => __DIR__ . '/private/private.key',
                    'ttl_access' => 'PT1H',
                    'ttl_refresh' => 'P1M',
                ],
                'oauth' => [
                    'scopes' => [
                        'basic',
                    ],
                ],
            ]);
        }
    ]);
};
