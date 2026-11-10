<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Deal;
use InvalidArgumentException;
use Throwable;

final class DealService
{
    private const ALLOWED_TYPES = ['permanent', 'time_limited', 'coupon'];

    private Deal $deals;
    private UploadService $uploads;
    private SlugService $slugs;

    public function __construct(?Deal $deals = null, ?UploadService $uploads = null, ?SlugService $slugs = null)
    {
        $this->deals = $deals ?? new Deal();
        $this->uploads = $uploads ?? new UploadService();
        $this->slugs = $slugs ?? new SlugService();
    }

    public function active(?int $limit = 3, ?string $type = null): array
    {
        $filteredType = ($type !== null && in_array($type, self::ALLOWED_TYPES, true)) ? $type : null;
        return $this->deals->activeVisible($limit, $filteredType);
    }

    public function getVisibleDeals(?int $limit = null, ?string $type = null): array
    {
        return $this->active($limit, $type);
    }

    public function getAdminDealsWithStatus(): array
    {
        $deals = $this->deals->allOrdered();
        $now = time();

        foreach ($deals as &$deal) {
            $deal['computed_status'] = $this->computeStatus($deal, $now);
        }

        return $deals;
    }

    public function find(int $id): ?array
    {
        return $this->deals->find($id);
    }

    public function save(?int $id, array $input, ?array $imageFile): int
    {
        $title = trim((string) ($input['title'] ?? ''));
        $type = trim((string) ($input['type'] ?? 'time_limited'));

        if ($title === '') {
            throw new InvalidArgumentException('Deal title is required.');
        }

        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('Invalid deal type.');
        }

        $couponCode = strtoupper(trim((string) ($input['coupon_code'] ?? '')));
        if ($type === 'coupon' && $couponCode === '') {
            throw new InvalidArgumentException('Coupon code is required for coupon deals.');
        }

        $startDatetime = $this->normalizeDatetime($input['start_datetime'] ?? null);
        $endDatetime = $this->normalizeDatetime($input['end_datetime'] ?? null);
        if ($startDatetime !== null && $endDatetime !== null && strtotime($endDatetime) < strtotime($startDatetime)) {
            throw new InvalidArgumentException('End date/time must be after start date/time.');
        }

        $existing = $id !== null ? $this->deals->find($id) : null;
        if ($id !== null && $existing === null) {
            throw new InvalidArgumentException('Deal not found.');
        }

        $newImagePath = null;
        if ($this->uploads->hasUploadedFile($imageFile)) {
            $newImagePath = $this->uploads->storeImage($imageFile, 'deals');
        }

        $payload = [
            'title' => $title,
            'slug' => $this->slugs->uniqueFor($this->deals, $title, $id),
            'description' => trim((string) ($input['description'] ?? '')) ?: null,
            'type' => $type,
            'discount_label' => trim((string) ($input['discount_label'] ?? '')) ?: null,
            'coupon_code' => $type === 'coupon' ? $couponCode : ($couponCode !== '' ? $couponCode : null),
            'image_path' => $newImagePath ?? ($existing['image_path'] ?? null),
            'start_datetime' => $type === 'permanent' ? null : $startDatetime,
            'end_datetime' => $type === 'permanent' ? null : $endDatetime,
            'is_active' => !empty($input['is_active']) ? 1 : 0,
            'sort_order' => max(0, (int) ($input['sort_order'] ?? 0)),
        ];

        try {
            if ($id === null) {
                $dealId = (int) $this->deals->create($payload);
            } else {
                $this->deals->update($id, $payload);
                $dealId = $id;
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

        return $dealId;
    }

    public function delete(int $id): void
    {
        $deal = $this->deals->find($id);
        if ($deal === null) {
            return;
        }

        $this->deals->delete($id);
        $this->uploads->deleteImage($deal['image_path'] ?? null);
    }

    private function normalizeDatetime(mixed $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));
        if ($trimmed === '') {
            return null;
        }

        $timestamp = strtotime($trimmed);
        if ($timestamp === false) {
            throw new InvalidArgumentException('Invalid date/time format provided.');
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function computeStatus(array $deal, int $now): string
    {
        if (empty($deal['is_active'])) {
            return 'Inactive';
        }

        if (!empty($deal['start_datetime']) && strtotime((string) $deal['start_datetime']) > $now) {
            return 'Scheduled';
        }

        if (!empty($deal['end_datetime']) && strtotime((string) $deal['end_datetime']) < $now) {
            return 'Expired';
        }

        return 'Active';
    }
}

