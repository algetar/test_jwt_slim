<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use App\Infrastructure\Persistence\User\AccessTokenRepository;
use App\Infrastructure\Persistence\User\ClientRepository;
use App\Infrastructure\Persistence\User\RefreshTokenRepository;
use App\Infrastructure\Persistence\User\ScopeRepository;
use App\Infrastructure\Persistence\User\UserRepository;
use DI\ContainerBuilder;
use League\Event\EmitterAwareInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use FaaPz\PDO\Database;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        Database::class => function (ContainerInterface $c) {
            $db = $c->get(SettingsInterface::class)->get('db');
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $db['host'],
                $db['port'],
                $db['dbname'],
            );
            $usr = $db['user'];
            $pwd = $db['password'];

            return new Database($dsn, $usr, $pwd);
        },
        AuthorizationServer::class => function (ContainerInterface $c): AuthorizationServer {
            /** @var Settings $settings */
            $settings = $c->get(SettingsInterface::class);
            $jwt = $settings->get('jwt');
            $auth = $settings->get('oauth');

            $clientRepository = $c->get(ClientRepository::class);
            $accessTokenRepository = $c->get(AccessTokenRepository::class);
            $scopeRepository = new ScopeRepository(array_flip($auth['scopes']));

            // Setup the authorization server
            return new AuthorizationServer(
                $clientRepository,                 // instance of ClientRepositoryInterface
                $accessTokenRepository,            // instance of AccessTokenRepositoryInterface
                $scopeRepository,                  // instance of ScopeRepositoryInterface
                $jwt['private_key'],               // path to private key
                $jwt['encryption_key'],            // encryption key
            );
        },
    ]);
};
