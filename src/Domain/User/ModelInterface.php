<?php

declare(strict_types=1);

namespace App\Domain\User;

interface ModelInterface
{
    public static function createFromArray(array $data);
}