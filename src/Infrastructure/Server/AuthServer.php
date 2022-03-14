<?php
declare(strict_types=1);

namespace App\Infrastructure\Server;

use App\Application\Settings\SettingsInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;

class AuthServer extends AuthorizationServer
{
    public function __construct(
        ClientRepositoryInterface $clientRepository,
        AccessTokenRepositoryInterface $accessTokenRepository,
        ScopeRepositoryInterface $scopeRepository,
        SettingsInterface $settings,
        ResponseTypeInterface $responseType = null
    ) {
        $jwt = $settings->get('jwt');
        parent::__construct(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $jwt['private_key'],
            $jwt['encryption_key'],
            $responseType
        );
    }
}