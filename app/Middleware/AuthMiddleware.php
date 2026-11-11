<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class AuthMiddleware
{
    public function handle(Request $request): ?Response
    {
        if (Auth::check() === null) {
            if ($request->expectsJson()) {
                return Response::json([
                    'error' => 'Session expired. Please sign in again.',
                    'redirect' => url('/admin/login'),
                ], 401);
            }

            Session::flash('error', 'Please sign in to access the admin area.');
            return Response::redirect('/admin/login');
        }

        return null;
    }
}
