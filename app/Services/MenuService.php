<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemTag;
use App\Models\MenuItemVariant;
use App\Models\Tag;
use InvalidArgumentException;
use Throwable;

final class MenuService
{
    private MenuCategory $categories;
    private MenuItem $items;
    private MenuItemVariant $variants;
    private Tag $tags;
    private MenuItemTag $itemTags;
    private UploadService $uploads;
    private MenuPdfService $pdf;
    private SlugService $slugs;

    public function __construct(
        ?MenuCategory $categories = null,
        ?MenuItem $items = null,
        ?MenuItemVariant $variants = null,
        ?Tag $tags = null,
        ?MenuItemTag $itemTags = null,
        ?UploadService $uploads = null,
        ?MenuPdfService $pdf = null,
        ?SlugService $slugs = null,
    ) {
        $this->categories = $categories ?? new MenuCategory();
        $this->items = $items ?? new MenuItem();
        $this->variants = $variants ?? new MenuItemVariant();
        $this->tags = $tags ?? new Tag();
        $this->itemTags = $itemTags ?? new MenuItemTag();
        $this->uploads = $uploads ?? new UploadService();
        $this->pdf = $pdf ?? new MenuPdfService($this);
        $this->slugs = $slugs ?? new SlugService();
    }

    public function getPublicMenu(): array
    {
        $categories = $this->categories->activeOrdered();
        $items = $this->items->activeForMenu();
        $allVariants = $this->variants->all('sort_order', 'ASC');
        $tagsByItem = $this->itemTags->tagsGroupedByMenuItem();

        $variantsByItem = [];
        foreach ($allVariants as $variant) {
            $variantsByItem[(int) $variant['menu_item_id']][] = $variant;
        }

        $itemsByCategory = [];
        foreach ($items as $item) {
            $itemId = (int) $item['id'];
            $item['variants'] = $variantsByItem[$itemId] ?? [];
            $item['tags'] = $tagsByItem[$itemId] ?? [];
            $itemsByCategory[(int) $item['category_id']][] = $item;
        }

        $result = [];
        foreach ($categories as $category) {
            $category['items'] = $itemsByCategory[(int) $category['id']] ?? [];
            $result[] = $category;
        }

        return $result;
    }

    public function getMenuPreview(int $categoryLimit = 3, int $itemsPerCategory = 3): array
    {
        $categories = $this->categories->activeOrdered($categoryLimit);
        $result = [];

        foreach ($categories as $category) {
            $category['items'] = $this->items->activeForCategory((int) $category['id'], $itemsPerCategory);
            $result[] = $category;
        }

        return $result;
    }

    public function getFeaturedItems(int $limit = 6): array
    {
        $items = $this->items->featuredActive($limit);
        $tagsByItem = $this->itemTags->tagsGroupedByMenuItem();

        foreach ($items as &$item) {
            $itemId = (int) $item['id'];
            $item['tags'] = $tagsByItem[$itemId] ?? [];
            $item['variants'] = $this->variants->forMenuItem($itemId);
        }

        return $items;
    }

    public function getAllItemsAdmin(): array
    {
        return $this->items->allWithCategory();
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateItemsAdmin(array $options = []): array
    {
        return $this->items->paginateAdmin($options);
    }

    public function getAllCategoriesAdmin(): array
    {
        return $this->categories->allOrderedWithItemCounts();
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateCategoriesAdmin(array $options = []): array
    {
        return $this->categories->paginateAdmin($options);
    }

    public function getAllTags(): array
    {
        return $this->tags->all('name', 'ASC');
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateTagsAdmin(array $options = []): array
    {
        return $this->tags->paginateAdmin($options);
    }

    public function findCategory(int $id): ?array
    {
        return $this->categories->find($id);
    }

    public function findItemWithRelations(int $id): ?array
    {
        $item = $this->items->find($id);
        if ($item === null) {
            return null;
        }

        $item['variants'] = $this->variants->forMenuItem($id);
        $item['tag_ids'] = $this->itemTags->tagIdsForItem($id);

        return $item;
    }

    public function saveItem(?int $id, array $input, ?array $imageFile): int
    {
        $categoryId = (int) ($input['category_id'] ?? 0);
        $name = trim((string) ($input['name'] ?? ''));

        if ($categoryId <= 0 || $this->categories->find($categoryId) === null) {
            throw new InvalidArgumentException('Please select a valid menu category.');
        }

        if ($name === '') {
            throw new InvalidArgumentException('Dish name is required.');
        }

        $rawPrice = trim((string) ($input['price'] ?? ''));
        $price = $rawPrice === '' ? null : number_format((float) $rawPrice, 2, '.', '');
        if ($price !== null && (float) $price < 0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }

        $existing = $id !== null ? $this->items->find($id) : null;
        if ($id !== null && $existing === null) {
            throw new InvalidArgumentException('Menu item not found.');
        }

        $newImagePath = null;
        if ($this->uploads->hasUploadedFile($imageFile)) {
            $newImagePath = $this->uploads->storeImage($imageFile, 'menu');
        }

        $payload = [
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $this->slugs->uniqueFor($this->items, $name, $id),
            'description' => trim((string) ($input['description'] ?? '')) ?: null,
            'price' => $price,
            'image_path' => $newImagePath ?? ($existing['image_path'] ?? null),
            'is_featured' => !empty($input['is_featured']) ? 1 : 0,
            'is_available' => !empty($input['is_available']) ? 1 : 0,
            'is_active' => !empty($input['is_active']) ? 1 : 0,
            'sort_order' => max(0, (int) ($input['sort_order'] ?? 0)),
        ];

        $variantsInput = $this->parseVariantsInput($input);
        $tagIds = is_array($input['tag_ids'] ?? null) ? $input['tag_ids'] : [];

        try {
            $this->items->beginTransaction();

            if ($id === null) {
                $itemId = (int) $this->items->create($payload);
            } else {
                $this->items->update($id, $payload);
                $itemId = $id;
            }

            $this->variants->deleteByMenuItem($itemId);
            foreach ($variantsInput as $index => $variant) {
                $this->variants->create([
                    'menu_item_id' => $itemId,
                    'name' => $variant['name'],
                    'price' => $variant['price'],
                    'is_available' => $variant['is_available'],
                    'sort_order' => $index,
                ]);
            }

            $this->itemTags->syncForItem($itemId, $tagIds);
            $this->items->commit();
        } catch (Throwable $e) {
            $this->items->rollBack();
            if ($newImagePath !== null) {
                $this->uploads->deleteImage($newImagePath);
            }
            throw $e;
        }

        if ($newImagePath !== null && !empty($existing['image_path'])) {
            $this->uploads->deleteImage((string) $existing['image_path']);
        }

        $this->pdf->regenerateSafely();

        return $itemId;
    }

    public function deleteItem(int $id): void
    {
        $item = $this->items->find($id);
        if ($item === null) {
            return;
        }

        $this->items->delete($id);
        $this->uploads->deleteImage($item['image_path'] ?? null);
        $this->pdf->regenerateSafely();
    }

    public function saveCategory(?int $id, array $input): int
    {
        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('Category name is required.');
        }

        $payload = [
            'name' => $name,
            'slug' => $this->slugs->uniqueFor($this->categories, $name, $id),
            'description' => trim((string) ($input['description'] ?? '')) ?: null,
            'sort_order' => max(0, (int) ($input['sort_order'] ?? 0)),
            'is_active' => !empty($input['is_active']) ? 1 : 0,
        ];

        if ($id === null) {
            $categoryId = (int) $this->categories->create($payload);
        } else {
            $this->categories->update($id, $payload);
            $categoryId = $id;
        }

        $this->pdf->regenerateSafely();

        return $categoryId;
    }

    public function deleteCategory(int $id): void
    {
        $itemCount = $this->items->count(['category_id' => $id]);
        if ($itemCount > 0) {
            throw new InvalidArgumentException('Cannot delete category while menu items still belong to it.');
        }

        $this->categories->delete($id);
        $this->pdf->regenerateSafely();
    }

    public function createTag(array $input): int
    {
        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('Tag name is required.');
        }

        $color = trim((string) ($input['color'] ?? ''));
        if ($color !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            throw new InvalidArgumentException('Tag color must be a valid 6-digit hex code (e.g., #b45309).');
        }

        $id = (int) $this->tags->create([
            'name' => $name,
            'slug' => $this->slugs->uniqueFor($this->tags, $name),
            'color' => $color !== '' ? $color : null,
        ]);

        $this->pdf->regenerateSafely();

        return $id;
    }

    public function deleteTag(int $id): void
    {
        $this->tags->delete($id);
        $this->pdf->regenerateSafely();
    }

    /**
     * @return list<array{name: string, price: string, is_available: int}>
     */
    private function parseVariantsInput(array $input): array
    {
        $names = $input['variant_name'] ?? [];
        $prices = $input['variant_price'] ?? [];
        $availables = $input['variant_available'] ?? [];

        if (!is_array($names) || !is_array($prices)) {
            return [];
        }

        $parsed = [];
        foreach ($names as $idx => $rawName) {
            $name = trim((string) $rawName);
            $rawPrice = trim((string) ($prices[$idx] ?? ''));
            if ($name === '' && $rawPrice === '') {
                continue;
            }
            if ($name === '' || $rawPrice === '' || (float) $rawPrice < 0) {
                throw new InvalidArgumentException('Each variant row requires both a name and a non-negative price.');
            }

            $parsed[] = [
                'name' => $name,
                'price' => number_format((float) $rawPrice, 2, '.', ''),
                'is_available' => isset($availables[$idx]) ? 1 : 0,
            ];
        }

        return $parsed;
    }
}

