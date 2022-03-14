<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserNotFoundException;
use Exception;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use App\Domain\User\User;

final class UserRepository extends BaseRepository implements \App\Domain\User\UserRepository, UserRepositoryInterface
{
    protected string $table = 'users';
    protected string $className = User::class;

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        $result = $this->find()->get();

        if ($result instanceof User) {
            return [$result->getId() => $result];
        }

        return $result;
    }


    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User
    {
        try {
            return $this->find()->where('id', '=', $id)->get();
        } catch (Exception $e) {
            throw new UserNotFoundException('wrong user id', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        $user = $this->find()->where('username', '=', $username)->get();
        if ($user === null) {
            return null;
        }

        $isPasswordValid = $user->isValidPassword($password);
        if (!$isPasswordValid) {
            return null;
        }

        return new UserEntity((string) $user->getId());
    }
}
