<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GalleryItem;
use InvalidArgumentException;
use Throwable;

final class GalleryService
{
    private const ALLOWED_TYPES = ['photo', 'youtube'];

    private GalleryItem $gallery;
    private UploadService $uploads;

    public function __construct(?GalleryItem $gallery = null, ?UploadService $uploads = null)
    {
        $this->gallery = $gallery ?? new GalleryItem();
        $this->uploads = $uploads ?? new UploadService();
    }

    public function getPublicItems(?string $category = null): array
    {
        return $this->gallery->activeOrdered($category);
    }

    public function getPublicCategories(): array
    {
        return $this->gallery->distinctActiveCategories();
    }

    public function getAllAdmin(): array
    {
        return $this->gallery->allOrdered();
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateAdmin(array $options = [], ?string $type = null): array
    {
        $filteredType = ($type !== null && in_array($type, self::ALLOWED_TYPES, true)) ? $type : null;
        return $this->gallery->paginateAdmin($options, $filteredType);
    }

    public function find(int $id): ?array
    {
        return $this->gallery->find($id);
    }

    public function save(?int $id, array $input, ?array $imageFile): int
    {
        $existing = $id !== null ? $this->gallery->find($id) : null;
        if ($id !== null && $existing === null) {
            throw new InvalidArgumentException('Gallery item not found.');
        }

        $type = trim((string) ($input['type'] ?? ($existing['type'] ?? 'photo')));
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('Gallery item type must be photo or youtube.');
        }

        $newImagePath = null;
        $youtubeId = null;

        if ($type === 'photo') {
            if ($this->uploads->hasUploadedFile($imageFile)) {
                $newImagePath = $this->uploads->storeImage($imageFile, 'gallery');
            } elseif ($id === null || empty($existing['image_path'])) {
                throw new InvalidArgumentException('An image file is required for photo items.');
            }
        } else {
            $youtubeId = $this->extractYouTubeId((string) ($input['youtube_id'] ?? $input['youtube_url'] ?? ''));
            if ($youtubeId === null) {
                throw new InvalidArgumentException('Please enter a valid YouTube URL or 11-character video ID.');
            }
        }

        $payload = [
            'type' => $type,
            'title' => trim((string) ($input['title'] ?? '')) ?: null,
            'caption' => trim((string) ($input['caption'] ?? '')) ?: null,
            'category' => trim((string) ($input['category'] ?? '')) ?: null,
            'image_path' => $type === 'photo' ? ($newImagePath ?? $existing['image_path']) : null,
            'youtube_id' => $type === 'youtube' ? $youtubeId : null,
            'alt_text' => trim((string) ($input['alt_text'] ?? '')) ?: null,
            'sort_order' => max(0, (int) ($input['sort_order'] ?? 0)),
            'is_active' => !empty($input['is_active']) ? 1 : 0,
        ];

        try {
            if ($id === null) {
                $itemId = (int) $this->gallery->create($payload);
            } else {
                $this->gallery->update($id, $payload);
                $itemId = $id;
            }
        } catch (Throwable $e) {
            if ($newImagePath !== null) {
                $this->uploads->deleteImage($newImagePath);
            }
            throw $e;
        }

        if (($newImagePath !== null || $type === 'youtube') && !empty($existing['image_path'])) {
            $this->uploads->deleteImage((string) $existing['image_path']);
        }

        return $itemId;
    }

    public function delete(int $id): void
    {
        $item = $this->gallery->find($id);
        if ($item === null) {
            return;
        }

        $this->gallery->delete($id);
        $this->uploads->deleteImage($item['image_path'] ?? null);
    }

    public function extractYouTubeId(string $input): ?string
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $trimmed)) {
            return $trimmed;
        }

        $patterns = [
            '~(?:youtube\.com/watch\?(?:.*&)?v=|youtu\.be/|youtube\.com/embed/|youtube\.com/shorts/)([a-zA-Z0-9_-]{11})~i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $trimmed, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
