<?php

declare(strict_types=1);

use App\Core\Env;
use App\Core\Request;
use App\Core\Session;
use App\Services\CsrfService;

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $root = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        return $path === '' ? $root : $root . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        $storage = base_path('storage');
        return $path === '' ? $storage : $storage . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return CsrfService::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('url')) {
    function url(string $path = '/'): string
    {
        $base = Request::detectedBasePath();
        $normalized = '/' . ltrim($path, '/');
        return $base . ($normalized === '/' ? '/' : $normalized);
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $trimmed = ltrim($path, '/');
        $fullPath = base_path('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $trimmed));
        $version = is_file($fullPath) ? '?v=' . filemtime($fullPath) : '';

        return url('/assets/' . $trimmed) . $version;
    }
}

if (!function_exists('query_url')) {
    function query_url(string $path, array $params = []): string
    {
        $filtered = array_filter(
            $params,
            static fn (mixed $v): bool => $v !== null && $v !== ''
        );
        $base = url($path);

        return $filtered === [] ? $base : $base . '?' . http_build_query($filtered);
    }
}

if (!function_exists('pagination_window')) {
    /**
     * @return list<int|string>
     */
    function pagination_window(int $currentPage, int $totalPages): array
    {
        if ($totalPages <= 7) {
            return $totalPages <= 1 ? [1] : range(1, $totalPages);
        }

        $pages = [1];
        $start = max(2, $currentPage - 1);
        $end = min($totalPages - 1, $currentPage + 1);

        if ($currentPage <= 3) {
            $start = 2;
            $end = 4;
        } elseif ($currentPage >= $totalPages - 2) {
            $start = $totalPages - 3;
            $end = $totalPages - 1;
        }

        if ($start > 2) {
            $pages[] = '...';
        }
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }
        if ($end < $totalPages - 1) {
            $pages[] = '...';
        }
        $pages[] = $totalPages;

        return $pages;
    }
}

if (!function_exists('media_url')) {
    function media_url(?string $relativePath): string
    {
        if ($relativePath === null || $relativePath === '') {
            return '';
        }

        return url('/media/' . ltrim($relativePath, '/'));
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $default = null): mixed
    {
        return Session::pullFlash($key, $default);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): string
    {
        $value = Session::getOld($key, $default);
        return is_scalar($value) ? (string) $value : (string) $default;
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = (string) preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text !== '' ? $text : 'item-' . bin2hex(random_bytes(4));
    }
}

if (!function_exists('format_price')) {
    function format_price(mixed $amount, string $currency = 'Rs. '): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        $num = (float) $amount;
        $decimals = abs($num - round($num)) < 0.005 ? 0 : 2;
        $prefix = trim($currency) === 'Rs.' ? 'Rs. ' : $currency;

        return $prefix . number_format($num, $decimals);
    }
}

if (!function_exists('current_path')) {
    function current_path(): string
    {
        return Request::detectedPath();
    }
}

