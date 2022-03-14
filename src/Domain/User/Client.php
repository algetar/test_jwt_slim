<?php

declare(strict_types=1);

namespace App\Domain\User;

use Ramsey\Uuid\Uuid;

class Client implements ModelInterface
{
    private string $id;

    private string $name;

    private ?string $secret;

    private string $redirect;

    private bool $active;

    public function __construct(string $userId, string $name)
    {
        $this->id = $userId;
        $this->name = $name;
    }

    public static function create(string $name): Client
    {
        return new self(Uuid::uuid4()->toString(), $name);
    }

    /**
     * @param array $data
     * @return Client|Client[]|null
     */
    public static function createFromArray(array $data)
    {
        if ($data == []) {
            return null;
        }

        if (($data['id'] ?? null) !== null) {
            $client = new self((string) $data['id'], $data['name']);
            $client->setSecret($data['secret']);
            $client->setRedirect($data['redirect']);
            $client->setActive(! $data['revoked']);

            return $client;
        }

        $result = [];
        foreach ($data as $row) {
            $result[$row['id']] = self::createFromArray($row);
        }

        return $result;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(?string $secret): void
    {
        $this->secret = $secret;
    }

    public function getRedirect(): string
    {
        return $this->redirect;
    }

    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
