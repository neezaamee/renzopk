<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\UploadService;

final class MediaController extends Controller
{
    public function show(Request $request, string $folder, string $file): Response
    {
        $resolved = (new UploadService())->resolveMediaFile($folder, $file);
        if ($resolved === null) {
            return Response::abort(404);
        }

        return Response::file(
            $resolved['path'],
            $resolved['mime'],
            true,
            null,
            'public, max-age=31536000, immutable'
        );
    }
}
