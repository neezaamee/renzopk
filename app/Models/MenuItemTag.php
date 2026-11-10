<?php

declare(strict_types=1);

namespace App\Models;

final class MenuItemTag extends Model
{
    protected string $table = 'menu_item_tags';

    protected array $fillable = [
        'menu_item_id',
        'tag_id',
    ];

    public function tagIdsForItem(int $menuItemId): array
    {
        $stmt = $this->db->prepare('SELECT tag_id FROM menu_item_tags WHERE menu_item_id = :menu_item_id');
        $stmt->execute(['menu_item_id' => $menuItemId]);

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public function syncForItem(int $menuItemId, array $tagIds): void
    {
        $deleteStmt = $this->db->prepare('DELETE FROM menu_item_tags WHERE menu_item_id = :menu_item_id');
        $deleteStmt->execute(['menu_item_id' => $menuItemId]);

        if ($tagIds === []) {
            return;
        }

        $insertStmt = $this->db->prepare(
            'INSERT INTO menu_item_tags (menu_item_id, tag_id) VALUES (:menu_item_id, :tag_id)'
        );

        foreach (array_unique(array_map('intval', $tagIds)) as $tagId) {
            if ($tagId > 0) {
                $insertStmt->execute([
                    'menu_item_id' => $menuItemId,
                    'tag_id' => $tagId,
                ]);
            }
        }
    }

    public function tagsGroupedByMenuItem(): array
    {
        $stmt = $this->db->prepare(
            'SELECT mit.menu_item_id, t.id, t.name, t.slug, t.color
             FROM menu_item_tags mit
             INNER JOIN tags t ON t.id = mit.tag_id
             ORDER BY t.name ASC'
        );
        $stmt->execute();

        $grouped = [];
        foreach ($stmt->fetchAll() as $row) {
            $itemId = (int) $row['menu_item_id'];
            $grouped[$itemId][] = $row;
        }

        return $grouped;
    }
}

