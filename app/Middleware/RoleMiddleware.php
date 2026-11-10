<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

final class RoleMiddleware
{
    /** @param list<string> $allowedRoles */
    public function __construct(private readonly array $allowedRoles)
    {
    }

    public function handle(Request $request): ?Response
    {
        $admin = (new AuthService())->currentAdmin();
        if ($admin === null) {
            return Response::redirect('/admin/login');
        }

        if (!in_array($admin['role'] ?? '', $this->allowedRoles, true)) {
            return Response::abort(403, 'You do not have permission to access this resource.');
        }

        return null;
    }
}

