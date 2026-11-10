<?php
$isEdit = is_array($item);
$selectedTagIds = $isEdit ? ($item['tag_ids'] ?? []) : (is_array(\App\Core\Session::getOld('tag_ids', [])) ? \App\Core\Session::getOld('tag_ids', []) : []);
$variants = $isEdit ? ($item['variants'] ?? []) : [];
?>
<div class="card">
    <form method="POST" action="<?= e(url($actionUrl)) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php $currentCat = (string) old('category_id', (string) ($item['category_id'] ?? '')); ?>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= e((string) $cat['id']) ?>" <?= $currentCat === (string) $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="name">Dish Name *</label>
                <input id="name" type="text" name="name" value="<?= e(old('name', (string) ($item['name'] ?? ''))) ?>" required>
            </div>
            <div class="field">
                <label for="price">Base Price (leave empty if variant-only)</label>
                <input id="price" type="number" step="0.01" min="0" name="price" value="<?= e(old('price', (string) ($item['price'] ?? ''))) ?>">
            </div>
            <div class="field">
                <label for="sort_order">Sort Order</label>
                <input id="sort_order" type="number" min="0" name="sort_order" value="<?= e(old('sort_order', (string) ($item['sort_order'] ?? '0'))) ?>">
            </div>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= e(old('description', (string) ($item['description'] ?? ''))) ?></textarea>
        </div>

        <div class="field">
            <label for="image">Dish Image (JPEG, PNG, WebP, max 5MB)</label>
            <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
            <?php if ($isEdit && !empty($item['image_path'])): ?>
                <div style="margin-top:8px;"><img src="<?= e(media_url($item['image_path'])) ?>" alt="" class="thumb"></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label>Tags</label>
            <div class="actions">
                <?php foreach ($tags as $tag): ?>
                    <label style="font-weight:400;display:flex;align-items:center;gap:6px;">
                        <input type="checkbox" name="tag_ids[]" value="<?= e((string) $tag['id']) ?>" <?= in_array((int) $tag['id'], array_map('intval', $selectedTagIds), true) ? 'checked' : '' ?>>
                        <?= e($tag['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="field">
            <div class="actions" style="justify-content:space-between;">
                <label style="margin:0;">Portion / Size Variants</label>
                <button type="button" class="btn btn-secondary btn-sm" data-add-variant>+ Add Variant</button>
            </div>
            <div data-variants-container style="margin-top:10px;">
                <?php foreach ($variants as $idx => $variant): ?>
                    <div class="form-grid" data-variant-row="1">
                        <div class="field">
                            <label>Variant Name</label>
                            <input type="text" name="variant_name[<?= e((string) $idx) ?>]" value="<?= e($variant['name']) ?>">
                        </div>
                        <div class="field">
                            <label>Price</label>
                            <input type="number" step="0.01" min="0" name="variant_price[<?= e((string) $idx) ?>]" value="<?= e((string) $variant['price']) ?>">
                        </div>
                        <div class="field" style="display:flex;align-items:center;gap:10px;padding-top:24px;">
                            <label style="margin:0;"><input type="checkbox" name="variant_available[<?= e((string) $idx) ?>]" value="1" <?= !empty($variant['is_available']) ? 'checked' : '' ?>> Available</label>
                            <button type="button" class="btn btn-danger btn-sm" data-remove-variant>Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="actions" style="margin:16px 0;">
            <label><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || !empty($item['is_active'])) ? 'checked' : '' ?>> Visible on Menu</label>
            <label><input type="checkbox" name="is_available" value="1" <?= (!$isEdit || !empty($item['is_available'])) ? 'checked' : '' ?>> In Stock / Available</label>
            <label><input type="checkbox" name="is_featured" value="1" <?= ($isEdit && !empty($item['is_featured'])) ? 'checked' : '' ?>> Featured on Home</label>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Save &amp; Regenerate PDF</button>
            <a href="<?= e(url('/admin/menu')) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
