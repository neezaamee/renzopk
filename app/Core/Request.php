<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    private static string $cachedBasePath = '';
    private static string $cachedPath = '/';

    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $post,
        private readonly array $files,
        private readonly array $server,
    ) {
    }

    public static function capture(): self
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $basePath = self::resolveBasePath();
        $uriPath = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');

        if ($basePath !== '' && str_starts_with($uriPath, $basePath)) {
            $uriPath = substr($uriPath, strlen($basePath)) ?: '/';
        }

        $normalizedPath = '/' . trim($uriPath, '/');
        if ($normalizedPath !== '/') {
            $normalizedPath = rtrim($normalizedPath, '/');
        }

        self::$cachedBasePath = $basePath;
        self::$cachedPath = $normalizedPath;

        return new self(
            method: $method,
            path: $normalizedPath,
            query: $_GET,
            post: $_POST,
            files: $_FILES,
            server: $_SERVER,
        );
    }

    public static function detectedBasePath(): string
    {
        return self::$cachedBasePath;
    }

    public static function detectedPath(): string
    {
        return self::$cachedPath;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function queryInt(string $key, int $default, int $min = 1, int $max = 100): int
    {
        $raw = $this->query[$key] ?? null;
        if ($raw === null || $raw === '' || !is_numeric($raw)) {
            return $default;
        }

        return max($min, min($max, (int) $raw));
    }

    /**
     * @param list<int> $allowedPerPage
     * @return array{page: int, per_page: int, q: string, sort: string, dir: string}
     */
    public function tableParams(array $allowedPerPage = [10, 25, 50, 100], int $defaultPerPage = 10): array
    {
        $page = $this->queryInt('page', 1, 1, 100000);
        $perPage = $this->queryInt('per_page', $defaultPerPage, 1, max($allowedPerPage));
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = $defaultPerPage;
        }

        $q = trim((string) ($this->query['q'] ?? ''));
        $sort = trim((string) ($this->query['sort'] ?? ''));
        $dir = strtolower(trim((string) ($this->query['dir'] ?? 'asc'))) === 'desc' ? 'desc' : 'asc';

        return [
            'page' => $page,
            'per_page' => $perPage,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ];
    }

    public function expectsJson(): bool
    {
        if (($this->query['format'] ?? '') === 'json') {
            return true;
        }

        if (strtolower((string) ($this->header('X-Requested-With') ?? '')) === 'xmlhttprequest') {
            return true;
        }

        return str_contains(strtolower((string) ($this->header('Accept') ?? '')), 'application/json');
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function allPost(): array
    {
        return $this->post;
    }

    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;
        return is_array($file) ? $file : null;
    }

    public function header(string $name): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        $value = $this->server[$normalized] ?? null;
        return is_string($value) ? $value : null;
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function schemeAndHost(): string
    {
        $configured = rtrim((string) Env::get('APP_URL', ''), '/');
        if ($configured !== '' && filter_var($configured, FILTER_VALIDATE_URL) !== false) {
            return $configured;
        }

        $https = !empty($this->server['HTTPS']) && strtolower((string) $this->server['HTTPS']) !== 'off';
        $forwarded = strtolower((string) ($this->server['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
        $scheme = ($https || $forwarded || (string) ($this->server['SERVER_PORT'] ?? '') === '443') ? 'https' : 'http';
        $host = (string) ($this->server['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host;
    }

    private static function resolveBasePath(): string
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($dir === '' || $dir === '.') {
            return '';
        }

        $requestPath = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
        if (str_ends_with($dir, '/public') && !str_starts_with($requestPath, $dir)) {
            $dir = substr($dir, 0, -strlen('/public')) ?: '';
        }

        return $dir;
    }
}

