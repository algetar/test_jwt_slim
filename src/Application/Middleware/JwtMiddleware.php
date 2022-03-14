<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class JwtMiddleware implements Middleware
{
    private AuthorizationServer $server;

    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    /**
     * @throws OAuthServerException
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->server->validateAuthorizationRequest($request);

        return $handler->handle($request);
    }
}