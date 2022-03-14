<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    public function __construct(array $scopes)
    {
        ScopeEntity::$scopes = $scopes;
    }

    /**
     * @inheritDoc
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        if (ScopeEntity::hasScope($identifier)) {
            return new ScopeEntity($identifier);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array {
        $filteredScopes = [];
        /** @var ScopeEntity $scope */
        foreach ($scopes as $scope) {
            $hasScope = ScopeEntity::hasScope($scope->getIdentifier());
            if ($hasScope) {
                $filteredScopes[] = $scope;
            }
        }

        return $filteredScopes;
    }
}
