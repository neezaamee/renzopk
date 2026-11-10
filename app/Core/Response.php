<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public function __construct(
        private readonly string $content = '',
        private readonly int $status = 200,
        private readonly array $headers = [],
        private readonly ?string $filePath = null,
    ) {
    }

    public static function html(string $content, int $status = 200): self
    {
        return new self(
            content: $content,
            status: $status,
            headers: ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }

    public static function xml(string $xml, int $status = 200): self
    {
        return new self(
            content: $xml,
            status: $status,
            headers: ['Content-Type' => 'application/xml; charset=UTF-8'],
        );
    }

    public static function text(string $text, int $status = 200): self
    {
        return new self(
            content: $text,
            status: $status,
            headers: ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }

    public static function redirect(string $path, int $status = 302): self
    {
        $target = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : url($path);

        return new self(
            status: $status,
            headers: ['Location' => $target],
        );
    }

    public static function file(
        string $filePath,
        string $contentType,
        bool $inline = true,
        ?string $downloadName = null,
        ?string $cacheControl = null
    ): self {
        $headers = [
            'Content-Type' => $contentType,
            'Content-Length' => (string) filesize($filePath),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => $cacheControl ?? 'public, max-age=3600',
        ];

        if ($downloadName !== null) {
            $disposition = $inline ? 'inline' : 'attachment';
            $headers['Content-Disposition'] = sprintf('%s; filename="%s"', $disposition, $downloadName);
        }

        return new self(
            status: 200,
            headers: $headers,
            filePath: $filePath,
        );
    }

    public static function abort(int $status, string $message = ''): self
    {
        $titles = [
            403 => 'Forbidden',
            404 => 'Page Not Found',
            419 => 'Session Expired (Invalid CSRF Token)',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
        ];

        $title = $titles[$status] ?? 'Error';
        $html = View::render('errors/http', [
            'status' => $status,
            'title' => $title,
            'message' => $message !== '' ? $message : $title,
        ]);

        return self::html($html, $status);
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        if ($this->filePath !== null && is_file($this->filePath)) {
            readfile($this->filePath);
            return;
        }

        echo $this->content;
    }
}

