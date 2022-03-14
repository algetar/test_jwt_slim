<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\AuthCode;
use Exception;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository extends BaseRepository implements AuthCodeRepositoryInterface
{
    protected string $table = 'oauth_auth_codes';
    protected string $className = AuthCode::class;

    /**
     * @inheritDoc
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        if ($this->exists($authCodeEntity->getIdentifier())) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $authCode = AuthCode::createFromArray([
            'id' => $authCodeEntity->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'scopes' => $authCodeEntity->getScopes(),
            'revoked' => false,
            'expires_at' => $authCodeEntity->getExpiryDateTime(),
        ]);

        $authCode->insert($this->db);
    }

    /**
     * @inheritDoc
     */
    public function revokeAuthCode($codeId)
    {
        $statement = $this->db->delete($this->table);
        $statement->where('id', '=', $codeId);
        $statement->execute();
    }

    /**
     * @inheritDoc
     */
    public function isAuthCodeRevoked($codeId)
    {
        return $this->exists($codeId);
    }

    private function exists($id): bool
    {
        return $this->find()->where('id', '=', $id)->get() ?? false;
    }
}