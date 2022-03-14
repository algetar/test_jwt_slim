<?php

declare(strict_types=1);

namespace App\Domain\User;

use DateTimeImmutable;
use Exception;
use FaaPz\PDO\Database;

class RefreshToken implements ModelInterface
{
    private string $id;

    private string $accessTokenId;

    private bool $revoked = false;

    private DateTimeImmutable $expiresAt;

    private array $willUpdate = [];

    private string $table = 'oauth_refresh_tokens';

    public function __construct(string $id, string $accessTokenId, DateTimeImmutable $expiresAt)
    {
        $this->id = $id;
        $this->accessTokenId = $accessTokenId;
        $this->expiresAt = $expiresAt;
    }

    public function getAccessTokenId(): string
    {
        return $this->accessTokenId;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->willUpdate[] = 'revoked';

        $this->revoked = true;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @throws Exception
     */
    public static function createFromArray(?array $data)
    {
        if ($data === null) {
            return null;
        }

        if (($data['id'] ?? null) !== null) {
            return new RefreshToken(
                $data['id'],
                $data['access_token_id'],
                new DateTimeImmutable($data['expires_at']),
            );
        }

        $result = [];
        foreach ($data as $row) {
            $result[$row['id']] = self::createFromArray($row);
        }

        return $result;
    }

    public function update(Database $db)
    {
        $attributes = [];
        foreach ($this->willUpdate as $attribute) {
            switch ($attribute) {
                case 'id':
                    $attributes['id'] = $this->getId();
                    break;
                case 'access_token_id':
                    $attributes['access_token_id'] = $this->getAccessTokenId();
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
            'access_token_id' => $this->getAccessTokenId(),
            'expires_at' => $this->getExpiresAt()->format('Y-m-d H:i:s'),
        ])->into($this->table);

        $statement->execute();
    }
}
