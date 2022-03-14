<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use ClientTrait;
    use EntityTrait;

    public function __construct(string $identifier, string $name, string $redirectUri)
    {
        $this->setIdentifier($identifier);
        $this->name = $name;
        $this->redirectUri = explode(',', $redirectUri);
    }
}
