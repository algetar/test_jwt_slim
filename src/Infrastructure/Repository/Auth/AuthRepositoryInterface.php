<?php

namespace App\Infrastructure\Repository\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface AuthRepositoryInterface
{
    public function getAccessToken(Request $request): ?Response;
}