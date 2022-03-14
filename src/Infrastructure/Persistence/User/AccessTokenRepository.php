<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\AccessToken;
use DateTime;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository extends BaseRepository implements AccessTokenRepositoryInterface
{
    protected string $table = 'oauth_access_tokens';
    protected string $className = AccessToken::class;

    /**
     * @inheritDoc
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new AccessTokenEntity($clientEntity, $scopes, $userIdentifier);
    }

    /**
     * @inheritDoc
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $appAccessToken = new AccessToken(
            $accessTokenEntity->getIdentifier(),
            $accessTokenEntity->getUserIdentifier(),
            $accessTokenEntity->getClient()->getIdentifier(),
            $this->scopesToArray($accessTokenEntity->getScopes()),
            (new DateTime())->setTimestamp($accessTokenEntity->getExpiryDateTime()->getTimestamp())
        );

        $appAccessToken->insert($this->db);
    }

    private function scopesToArray($scopes): array
    {
        if (is_array($scopes)) {
            return $scopes;
        }

        return json_decode($scopes);
    }

    /**
     * @inheritDoc
     */
    public function revokeAccessToken($tokenId)
    {
        $accessToken = $this->find()->where('id', '=', $tokenId)->get();
        if ($accessToken === null) {
            return;
        }
        $accessToken->revoke();

        $accessToken->update($this->db);
    }

    /**
     * @inheritDoc
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $accessToken = $this->find()->where('id', '=', $tokenId)->get();
        if ($accessToken === null) {
            return true;
        }

        return $accessToken->isRevoked();
    }
}