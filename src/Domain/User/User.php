<?php

declare(strict_types=1);

namespace App\Domain\User;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use ReturnTypeWillChange;

class User implements JsonSerializable, ModelInterface
{
    private ?int $id;
    private string $username;
    private string $firstName;
    private string $lastName;
    private ?string $password;
    private ?string $token;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        int $id,
        string $username,
        string $firstName,
        string $lastName,
        ?string $password = null,
        ?string $token = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->username = strtolower($username);
        $this->firstName = ucfirst($firstName);
        $this->lastName = ucfirst($lastName);
        $this->password = $password;
        $this->token = $token;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function isValidPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    #[ArrayShape(['id' => 'int|null','username' => 'string','firstName' => 'string','lastName' => 'string'])]
    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }

    /**
     * @param array $data
     * @return User|User[]|null
     */
    public static function createFromArray(array $data)
    {
        if ($data == []) {
            return null;
        }

        if (($data['id'] ?? null) !== null) {
            return new User(
                $data['id'],
                $data['username'],
                $data['first_name'],
                $data['last_name'],
                $data['password'],
                $data['token'],
                $data['created_at'],
                $data['updated_at']
            );
        }

        $result = [];
        foreach ($data as $row) {
            $result[$row['id']] = self::createFromArray($row);
        }

        return $result;
    }
}
