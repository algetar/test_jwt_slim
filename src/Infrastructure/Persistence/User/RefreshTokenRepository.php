<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\RefreshToken;
use JetBrains\PhpStorm\Pure;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository extends BaseRepository implements RefreshTokenRepositoryInterface
{
    protected string $table = 'oauth_refresh_tokens';
    protected string $className = RefreshToken::class;
    #[Pure]
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshTokenEntity();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        if ($this->exists($refreshTokenEntity->getIdentifier())) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $id = $refreshTokenEntity->getIdentifier();
        $accessTokenId = $refreshTokenEntity->getAccessToken()->getIdentifier();
        $expiryDateTime = $refreshTokenEntity->getExpiryDateTime();
        $refreshToken = new RefreshToken($id, $accessTokenId, $expiryDateTime);

        $refreshToken->insert($this->db);
    }

    public function revokeRefreshToken($tokenId): void
    {
        $statement = $this->db->delete($this->table);
        $statement->where('id', '=', $tokenId);
        $statement->execute();
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        return $this->exists($tokenId);
    }

    private function exists($id): bool
    {
        return $this->find()->where('id', '=', $id)->get() ?? false;
    }
}
