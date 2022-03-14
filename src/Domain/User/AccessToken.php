<?php

declare(strict_types=1);

namespace App\Domain\User;

use DateTime;
use FaaPz\PDO\Database;

class AccessToken
{
    private string $id;

    private string $userId;

    private string $clientId;

    private array $scopes;

    private bool $revoked;

    private DateTime $expiresAt;

    private array $willUpdate = [];

    private string $table = 'oauth_access_tokens';

    /**
     * Token constructor.
     * @param string $id
     * @param string $userId
     * @param string $clientId
     * @param array $scopes
     * @param DateTime $expiresAt
     */
    public function __construct(
        string $id,
        string $userId,
        string $clientId,
        array $scopes,
        DateTime $expiresAt
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->clientId = $clientId;
        $this->scopes = $scopes;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * @return DateTime
     */
    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function revoke()
    {
        $this->willUpdate[] = 'revoked';

        $this->revoked = true;
    }

    public function update(Database $db)
    {
        $attributes = [];
        foreach ($this->willUpdate as $attribute) {
            switch ($attribute) {
                case 'id':
                    $attributes['id'] = $this->getId();
                    break;
                case 'user_id':
                    $attributes['user_id'] = $this->getUserId();
                    break;
                case 'client_id':
                    $attributes['client_id'] = $this->getClientId();
                    break;
                case 'scopes':
                    $attributes['scopes'] = json_encode($this->getScopes());
                    break;
                case 'revoked':
                    $attributes['revoked'] = $this->isRevoked();
                    break;
                case 'expires_at':
                    $attributes['expires_at'] = $this->getExpiresAt()->format('Y-m-d H:i:s');
                    break;
            }
        }

        $statement = $db->update($attributes)->table($this->table);
        $statement->where('id', '=', $this->getId());
        $statement->execute();
    }

    public function insert(Database $db)
    {
        $statement = $db->insert([
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'client_id' => $this->getClientId(),
            'scopes' => json_encode($this->getScopes()),
            'expires_at' => $this->getExpiresAt()->format('Y-m-d H:i:s'),
        ])->into($this->table);

        $statement->execute();
    }
}
