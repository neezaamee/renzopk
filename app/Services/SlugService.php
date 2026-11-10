<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Model;

final class SlugService
{
    public function uniqueFor(Model $model, string $source, ?int $ignoreId = null): string
    {
        $base = slugify($source);
        $candidate = $base;
        $counter = 2;

        while (true) {
            $existing = $model->findBy('slug', $candidate);
            if ($existing === null || ($ignoreId !== null && (int) $existing['id'] === $ignoreId)) {
                return $candidate;
            }

            $candidate = $base . '-' . $counter;
            $counter++;
        }
    }
}

