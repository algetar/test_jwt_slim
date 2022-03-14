<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ScopeEntity implements ScopeEntityInterface
{
    use EntityTrait;

    public static array $scopes = [];

    public function __construct($name)
    {
        $this->setIdentifier($name);
    }

    public static function hasScope($id): bool
    {
        return $id === '*' || array_key_exists($id, static::$scopes);
    }

    /**
     * Get the data that should be serialized to JSON.
     */
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
