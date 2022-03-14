<?php
declare(strict_types=1);

namespace App\Domain\User;

use DateTimeImmutable;
use Exception;
use FaaPz\PDO\Database;

class AuthCode implements ModelInterface
{
    private string $id;
    private int $userId;
    private int $clientId;
    private array $scopes;
    private bool $revoked;
    private DateTimeImmutable $expiresAt;
    private array $willUpdate = [];
    private string $table = 'oauth_auth_codes';

    public function __construct(
        string $id,
        int $userId,
        int $clientId,
        array $scopes,
        bool $revoked,
        DateTimeImmutable $expires_at
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->clientId = $clientId;
        $this->scopes = $scopes;
        $this->revoked = $revoked;
        $this->expiresAt = $expires_at;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getClientId(): int
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

    public function revoke(): void
    {
        $this->willUpdate[] = 'revoked';

        $this->revoked = true;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @throws Exception
     */
    public static function createFromArray(?array $data)
    {
        if ($data == null) {
            return null;
        }

        if (($data['id'] ?? null) !== null) {
            return new AuthCode(
                $data['id'],
                (int) $data['user_id'],
                (int) $data['client_id'],
                $data['scopes'] ? json_decode($data['scopes']) : [],
                $data['revoked'],
                ($data['expires_at'] instanceof \DateTimeImmutable) ?
                    $data['expires_at'] :
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

        try {
            $statement->execute();
        } catch (Exception $e) {
            if ($e->getCode() != 55000) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }
    }
}