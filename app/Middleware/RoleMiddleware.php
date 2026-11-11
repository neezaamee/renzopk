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
            if ($request->expectsJson()) {
                return Response::json([
                    'error' => 'Session expired. Please sign in again.',
                    'redirect' => url('/admin/login'),
                ], 401);
            }

            return Response::redirect('/admin/login');
        }

        if (!in_array($admin['role'] ?? '', $this->allowedRoles, true)) {
            if ($request->expectsJson()) {
                return Response::json([
                    'error' => 'You do not have permission to access this resource.',
                ], 403);
            }

            return Response::abort(403, 'You do not have permission to access this resource.');
        }

        return null;
    }
}

