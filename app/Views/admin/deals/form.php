<?php
$isEdit = is_array($deal);
$toInputDatetime = static fn (?string $dt): string => ($dt !== null && $dt !== '') ? date('Y-m-d\TH:i', strtotime($dt)) : '';
$currentType = (string) old('type', (string) ($deal['type'] ?? 'time_limited'));
?>
<div class="card" style="max-width:780px;">
    <form method="POST" action="<?= e(url($actionUrl)) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label for="d_title">Title *</label>
                <input id="d_title" type="text" name="title" value="<?= e(old('title', (string) ($deal['title'] ?? ''))) ?>" required>
            </div>
            <div class="field">
                <label for="d_type">Deal Type *</label>
                <select id="d_type" name="type">
                    <option value="permanent" <?= $currentType === 'permanent' ? 'selected' : '' ?>>Permanent</option>
                    <option value="time_limited" <?= $currentType === 'time_limited' ? 'selected' : '' ?>>Time Limited</option>
                    <option value="coupon" <?= $currentType === 'coupon' ? 'selected' : '' ?>>Coupon Code</option>
                </select>
            </div>
            <div class="field">
                <label for="d_label">Discount Badge Label</label>
                <input id="d_label" type="text" name="discount_label" value="<?= e(old('discount_label', (string) ($deal['discount_label'] ?? ''))) ?>" placeholder="e.g. 20% OFF">
            </div>
            <div class="field">
                <label for="d_code">Coupon Code (required for Coupon type)</label>
                <input id="d_code" type="text" name="coupon_code" value="<?= e(old('coupon_code', (string) ($deal['coupon_code'] ?? ''))) ?>" placeholder="RENZO20">
            </div>
            <div class="field">
                <label for="d_start">Start Date/Time (optional)</label>
                <input id="d_start" type="datetime-local" name="start_datetime" value="<?= e(old('start_datetime', $toInputDatetime($deal['start_datetime'] ?? null))) ?>">
            </div>
            <div class="field">
                <label for="d_end">End Date/Time (optional)</label>
                <input id="d_end" type="datetime-local" name="end_datetime" value="<?= e(old('end_datetime', $toInputDatetime($deal['end_datetime'] ?? null))) ?>">
            </div>
        </div>

        <div class="field">
            <label for="d_desc">Description</label>
            <textarea id="d_desc" name="description" rows="3"><?= e(old('description', (string) ($deal['description'] ?? ''))) ?></textarea>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="d_image">Promo Image (optional)</label>
                <input id="d_image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <?php if ($isEdit && !empty($deal['image_path'])): ?>
                    <div style="margin-top:8px;"><img src="<?= e(media_url($deal['image_path'])) ?>" alt="" class="thumb"></div>
                <?php endif; ?>
            </div>
            <div class="field">
                <label for="d_sort">Sort Order</label>
                <input id="d_sort" type="number" min="0" name="sort_order" value="<?= e(old('sort_order', (string) ($deal['sort_order'] ?? '0'))) ?>">
            </div>
        </div>

        <div class="field">
            <label><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || !empty($deal['is_active'])) ? 'checked' : '' ?>> Active</label>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Save Deal</button>
            <a href="<?= e(url('/admin/deals')) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

