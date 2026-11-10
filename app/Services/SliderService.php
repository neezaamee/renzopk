<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SliderItem;
use InvalidArgumentException;
use Throwable;

final class SliderService
{
    private SliderItem $sliders;
    private UploadService $uploads;

    public function __construct(?SliderItem $sliders = null, ?UploadService $uploads = null)
    {
        $this->sliders = $sliders ?? new SliderItem();
        $this->uploads = $uploads ?? new UploadService();
    }

    public function getActiveSlides(): array
    {
        return $this->sliders->activeOrdered();
    }

    public function getAllAdmin(): array
    {
        return $this->sliders->allOrdered();
    }

    public function find(int $id): ?array
    {
        return $this->sliders->find($id);
    }

    public function save(?int $id, array $input, ?array $imageFile): int
    {
        $existing = $id !== null ? $this->sliders->find($id) : null;
        if ($id !== null && $existing === null) {
            throw new InvalidArgumentException('Slider item not found.');
        }

        $title = trim((string) ($input['title'] ?? ''));
        $ctaUrl = $this->sanitizeCtaUrl((string) ($input['cta_url'] ?? ''));

        $newImagePath = null;
        if ($this->uploads->hasUploadedFile($imageFile)) {
            $newImagePath = $this->uploads->storeImage($imageFile, 'sliders');
        } elseif ($id === null && $title === '') {
            throw new InvalidArgumentException('Please provide a slide headline or upload a banner image.');
        }

        $payload = [
            'title' => $title !== '' ? $title : null,
            'subtitle' => trim((string) ($input['subtitle'] ?? '')) ?: null,
            'cta_text' => trim((string) ($input['cta_text'] ?? '')) ?: null,
            'cta_url' => $ctaUrl,
            'image_path' => $newImagePath ?? ($existing['image_path'] ?? null),
            'alt_text' => trim((string) ($input['alt_text'] ?? '')) ?: null,
            'sort_order' => max(0, (int) ($input['sort_order'] ?? 0)),
            'is_active' => !empty($input['is_active']) ? 1 : 0,
        ];

        try {
            if ($id === null) {
                $slideId = (int) $this->sliders->create($payload);
            } else {
                $this->sliders->update($id, $payload);
                $slideId = $id;
            }
        } catch (Throwable $e) {
            if ($newImagePath !== null) {
                $this->uploads->deleteImage($newImagePath);
            }
            throw $e;
        }

        if ($newImagePath !== null && !empty($existing['image_path'])) {
            $this->uploads->deleteImage((string) $existing['image_path']);
        }

        return $slideId;
    }

    public function delete(int $id): void
    {
        $slide = $this->sliders->find($id);
        if ($slide === null) {
            return;
        }

        $this->sliders->delete($id);
        $this->uploads->deleteImage($slide['image_path'] ?? null);
    }

    private function sanitizeCtaUrl(string $raw): ?string
    {
        $url = trim($raw);
        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '#') || (str_starts_with($url, '/') && !str_starts_with($url, '//'))) {
            return $url;
        }

        if (preg_match('#^https?://#i', $url) && filter_var($url, FILTER_VALIDATE_URL) !== false) {
            return $url;
        }

        throw new InvalidArgumentException('Button URL must be a relative path (/menu, #contact) or valid http(s) URL.');
    }
}
