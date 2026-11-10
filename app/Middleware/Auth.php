<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthService;

final class Auth
{
    public static function check(): ?array
    {
        return (new AuthService())->currentAdmin();
    }
}

