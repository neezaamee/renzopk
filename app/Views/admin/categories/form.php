<?php $isEdit = is_array($category); ?>
<div class="card" style="max-width:640px;">
    <form method="POST" action="<?= e(url($actionUrl)) ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label for="cat_name">Category Name *</label>
            <input id="cat_name" type="text" name="name" value="<?= e(old('name', (string) ($category['name'] ?? ''))) ?>" required>
        </div>
        <div class="field">
            <label for="cat_desc">Description</label>
            <textarea id="cat_desc" name="description" rows="3"><?= e(old('description', (string) ($category['description'] ?? ''))) ?></textarea>
        </div>
        <div class="field">
            <label for="cat_sort">Sort Order</label>
            <input id="cat_sort" type="number" min="0" name="sort_order" value="<?= e(old('sort_order', (string) ($category['sort_order'] ?? '0'))) ?>">
        </div>
        <div class="field">
            <label><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || !empty($category['is_active'])) ? 'checked' : '' ?>> Active</label>
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-primary">Save Category</button>
            <a href="<?= e(url('/admin/menu/categories')) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

