<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Env;
use InvalidArgumentException;
use RuntimeException;

final class UploadService
{
    private const ALLOWED_FOLDERS = ['menu', 'deals', 'gallery', 'sliders'];

    private const ALLOWED_MIME_MAP = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function storeImage(array $file, string $folder): string
    {
        if (!in_array($folder, self::ALLOWED_FOLDERS, true)) {
            throw new InvalidArgumentException('Unsupported upload folder.');
        }

        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Image upload failed (error code ' . $error . ').');
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            throw new InvalidArgumentException('Invalid uploaded file payload.');
        }

        $maxBytes = (int) Env::get('UPLOAD_MAX_BYTES', 5242880);
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > $maxBytes) {
            $maxMb = round($maxBytes / 1048576, 1);
            throw new InvalidArgumentException("Image size must be between 1 byte and {$maxMb} MB.");
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmpPath);
        $extension = self::ALLOWED_MIME_MAP[$mime] ?? null;
        if ($extension === null) {
            throw new InvalidArgumentException('Only JPEG, PNG, and WebP images are permitted.');
        }

        if (@getimagesize($tmpPath) === false) {
            throw new InvalidArgumentException('Uploaded file is not a valid image.');
        }

        $targetDir = storage_path('uploads' . DIRECTORY_SEPARATOR . $folder);
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException('Unable to create upload target directory.');
        }

        $baseName = bin2hex(random_bytes(16));

        if ($folder === 'gallery') {
            $webpPath = $targetDir . DIRECTORY_SEPARATOR . $baseName . '.webp';
            if ($this->convertFileToWebp($tmpPath, $mime, $webpPath)) {
                return $folder . '/' . $baseName . '.webp';
            }
        }

        $filename = $baseName . '.' . $extension;
        $destination = $targetDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmpPath, $destination)) {
            throw new RuntimeException('Failed to persist uploaded image.');
        }

        return $folder . '/' . $filename;
    }

    public function convertFileToWebp(string $sourcePath, string $mime, string $destination, int $quality = 82): bool
    {
        if (extension_loaded('gd') && function_exists('imagewebp')) {
            $image = match ($mime) {
                'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($sourcePath) : false,
                'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($sourcePath) : false,
                'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
                default => false,
            };

            if ($image !== false) {
                if (function_exists('imagepalettetotruecolor')) {
                    @imagepalettetotruecolor($image);
                }
                imagealphablending($image, true);
                imagesavealpha($image, true);
                $ok = imagewebp($image, $destination, $quality);
                imagedestroy($image);
                if ($ok && is_file($destination)) {
                    return true;
                }
            }
        }

        if (class_exists(\Imagick::class)) {
            try {
                $imagick = new \Imagick($sourcePath);
                $imagick->stripImage();
                $imagick->setImageFormat('webp');
                $imagick->setImageCompressionQuality($quality);
                $ok = $imagick->writeImage($destination);
                $imagick->clear();
                $imagick->destroy();
                return $ok && is_file($destination);
            } catch (\Throwable) {
                return false;
            }
        }

        return false;
    }

    public function hasUploadedFile(?array $file): bool
    {
        return is_array($file)
            && isset($file['error'])
            && (int) $file['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function deleteImage(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '' || !str_contains($relativePath, '/')) {
            return;
        }

        [$folder, $filename] = explode('/', ltrim($relativePath, '/'), 2);
        if (!in_array($folder, self::ALLOWED_FOLDERS, true)) {
            return;
        }

        if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/i', $filename)) {
            return;
        }

        $fullPath = storage_path('uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * @return array{path: string, mime: string}|null
     */
    public function resolveMediaFile(string $folder, string $filename): ?array
    {
        if (!in_array($folder, self::ALLOWED_FOLDERS, true)) {
            return null;
        }

        if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/i', $filename)) {
            return null;
        }

        $baseDir = realpath(storage_path('uploads'));
        $filePath = realpath(storage_path('uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename));

        if ($baseDir === false || $filePath === false || !is_file($filePath)) {
            return null;
        }

        if (!str_starts_with($filePath, $baseDir . DIRECTORY_SEPARATOR)) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($filePath);
        if (!array_key_exists($mime, self::ALLOWED_MIME_MAP)) {
            return null;
        }

        return [
            'path' => $filePath,
            'mime' => $mime,
        ];
    }
}
