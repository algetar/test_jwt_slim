<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Infrastructure\Persistence\User\UserEntity;
use App\Infrastructure\Persistence\User\UserRepository;
use FaaPz\PDO\Database;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Settings\SettingsInterface;
use App\Domain\User\UserRepository as UserRepositoryInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->post(
        '/api/auth/signup',
        function (Request $request, Response $response) use ($app) {
            $container = $app->getContainer();
            $settings = $container->get(SettingsInterface::class);

            /* @var AuthorizationServer $server */
            $server                 = $container->get(AuthorizationServer::class);
            $userRepository         = $container->get(UserRepositoryInterface::class);
            $refreshTokenRepository = $container->get(RefreshTokenRepositoryInterface::class);
            $jwt                    = $settings->get('jwt');

            try {
                $grant = new PasswordGrant(
                    $userRepository,           // instance of UserRepositoryInterface
                    $refreshTokenRepository    // instance of RefreshTokenRepositoryInterface
                );
                // refresh tokens will expire after ttl_refresh value (1 month)
                $grant->setRefreshTokenTTL(new \DateInterval($jwt['ttl_refresh']));

                // Enable the password grant on the server with a token TTL
                $server->enableGrantType(
                    $grant,
                    new DateInterval($jwt['ttl_access']) // access tokens will expire after ttl_access value
                );

                // Try to respond to the access token request
                return $server->respondToAccessTokenRequest($request, $response);
            } catch (OAuthServerException $exception) {
                // All instances of OAuthServerException can be converted to a PSR-7 response
                return $exception->generateHttpResponse($response);
            } catch (\Exception $exception) {
                // Catch unexpected exceptions
                $body = $response->getBody();
                $body->write($exception->getMessage());

                return $response->withStatus(500)->withBody($body);
            }
        }
    );

    $app->post(
        '/api/oauth/auth',
        function (Request $request, Response $response) use ($app) {
            $container = $app->getContainer();
            $settings = $container->get(SettingsInterface::class);

            /* @var AuthorizationServer $server */
            $server                 = $container->get(AuthorizationServer::class);
            $authCodeRepository     = $container->get(AuthCodeRepositoryInterface::class);
            $refreshTokenRepository = $container->get(RefreshTokenRepositoryInterface::class);
            $jwt                    = $settings->get('jwt');

            try {
                // Enable the authentication code grant on the server with a token TTL of 1 hour
                $server->enableGrantType(
                    new AuthCodeGrant(
                        $authCodeRepository,
                        $refreshTokenRepository,
                        new \DateInterval($jwt['ttl_access'])
                    ),
                    new \DateInterval($jwt['ttl_refresh'])
                );
                // Validate the HTTP request and return an AuthorizationRequest object.
                // The auth request object can be serialized into a user's session
                $authRequest = $server->validateAuthorizationRequest($request);

                $params = $request->getParsedBody();
                // Once the user has logged in set the user on the AuthorizationRequest
                $authRequest->setUser(new UserEntity($params['client_id']));

                // Once the user has approved or denied the client update the status
                // (true = approved, false = denied)
                $authRequest->setAuthorizationApproved(true);

                // Return the HTTP redirect response
                $server->completeAuthorizationRequest($authRequest, $response);

                $body = $response->getBody();
                $body->write(json_encode(['status' => 'ok']));

                return $response->withStatus(200)->withBody($body);
            } catch (OAuthServerException $exception) {
                // All instances of OAuthServerException can be converted to a PSR-7 response
                return $exception->generateHttpResponse($response);
            } catch (\Exception $exception) {
                // Catch unexpected exceptions
                $body = $response->getBody();
                $body->write($exception->getMessage());

                return $response->withStatus(500)->withBody($body);
            }
        }
    );

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/test', function (Request $request, Response $response) use ($app) {
        /** @var Database $db */
        $db = $app->getContainer()->get(Database::class);
        $repository = new UserRepository($db);
        $user = $repository->findUserOfId(1);
        $payload = json_encode($user);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/whoami', function (Request $request, Response $response) {
        /** @var Database $db */
        $payload = json_encode([
            'get_current_user' => get_current_user(),
            'whoami' => system('whoami'),
        ]);
        $response->getBody()->write($payload);

        return $response;
    });
};
