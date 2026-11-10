<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\CsrfService;

final class CsrfMiddleware
{
    public function handle(Request $request): ?Response
    {
        $token = (string) ($request->input('_token') ?? $request->header('X-CSRF-TOKEN') ?? '');

        if (!CsrfService::verify($token)) {
            return Response::abort(419, 'Invalid or missing CSRF security token. Please refresh and try again.');
        }

        return null;
    }
}

