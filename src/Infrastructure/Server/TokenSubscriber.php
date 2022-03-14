<?php

declare(strict_types=1);

namespace App\Infrastructure\Server;

use App\Infrastructure\Persistence\User\AccessTokenEntity;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use function DI\create;

class TokenSubscriber
{
    private ResourceServer $resourceServer;
    /**
     * TokenSubscriber constructor.
     * @param ResourceServer $resourceServer
     */
    public function __construct(ResourceServer $resourceServer)
    {
        $this->resourceServer = $resourceServer;
    }

    /**
     * @throws OAuthServerException
     */
    public function onAuth(Request $request): void
    {

        try {
            $serverRequestCreator = ServerRequestCreatorFactory::create();
            $psrRequest = $serverRequestCreator->createServerRequestFromGlobals();
            $psrRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
        } catch (OAuthServerException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new OAuthServerException(
                $exception->getMessage(),
                0,
                'unknown_error',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        $this->requestWithAuthData($request, $psrRequest);
    }
    /**
     * @param Request $request
     * @param ServerRequestInterface $psrRequest
     */
    private function requestWithAuthData(Request $request, ServerRequestInterface $psrRequest): void
    {
        $requestArray = $request->getBody();
        $requestArray['oauth_user_id'] = $psrRequest->getAttribute('oauth_user_id');
        $requestArray['oauth_access_token_id'] =  $psrRequest->getAttribute('oauth_access_token_id');
        $requestArray['oauth_client_id'] =  $psrRequest->getAttribute('oauth_client_id');
        $request->withParsedBody($requestArray);
    }

    public function onAuthException(Exception $exception): void
    {
        if (!($exception instanceof OAuthServerException)) {
            return;
        }

        //$response = new JsonResponse(['error' => $exception->getMessage()], $exception->getHttpStatusCode());
        //$event->setResponse($response);
    }
}