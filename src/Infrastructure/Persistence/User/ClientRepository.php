<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\Client;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    protected string $table = 'oauth_clients';
    protected string $className = Client::class;

    /**
     * @inheritDoc
     */
    public function getClientEntity(
        $clientIdentifier,
        $grantType = null,
        $clientSecret = null,
        $mustValidateSecret = true
    ): ?ClientEntityInterface {
        $client = $this->findActive($clientIdentifier);
        if ($client === null) {
            return null;
        }

        return new ClientEntity($clientIdentifier, $client->getName(), $client->getRedirect());
    }

    public function findActive($id): ?Client
    {
        return $this->find()
            ->where('user_id', '=', $id)
            ->getLast()
        ;
    }

    /**
     * @inheritDoc
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $client = $this->findActive($clientIdentifier);
        if ($client === null) {
            return false;
        }

        if ($clientSecret !== null) {
            return false;
        }

        return true;
    }
}
