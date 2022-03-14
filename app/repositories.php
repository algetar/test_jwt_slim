<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Domain\User\UserRepository as UserRepositoryInterface;
use App\Infrastructure\Persistence\User\AccessTokenRepository;
use App\Infrastructure\Persistence\User\AuthCodeRepository;
use App\Infrastructure\Persistence\User\ClientRepository;
use App\Infrastructure\Persistence\User\RefreshTokenRepository;
use App\Infrastructure\Persistence\User\ScopeRepository;
use App\Infrastructure\Persistence\User\UserRepository;
use DI\ContainerBuilder;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Psr\Container\ContainerInterface;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepositoryInterface::class => autowire(UserRepository::class),
        RefreshTokenRepositoryInterface::class => autowire(RefreshTokenRepository::class),
        ClientRepositoryInterface::class => autowire(ClientRepository::class),
        AccessTokenRepositoryInterface::class => autowire(AccessTokenRepository::class),
        AuthCodeRepositoryInterface::class => autowire(AuthCodeRepository::class),
        ScopeRepositoryInterface::class => static function (ContainerInterface $container): ScopeRepository {
            $oauth = $container->get(SettingsInterface::class)['oauth'];

            return new ScopeRepository($oauth['scopes']);
        },
    ]);
};
