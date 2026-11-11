<?php
$isEdit = is_array($item);
$currentType = (string) old('type', (string) ($item['type'] ?? 'photo'));
$currentCat = (string) old('category', (string) ($item['category'] ?? 'Food'));
?>
<div class="card" style="max-width:680px;">
    <form method="POST" action="<?= e(url($actionUrl)) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label for="g_type">Item Type *</label>
                <select id="g_type" name="type">
                    <option value="photo" <?= $currentType === 'photo' ? 'selected' : '' ?>>Photo Upload</option>
                    <option value="youtube" <?= $currentType === 'youtube' ? 'selected' : '' ?>>YouTube Video</option>
                </select>
            </div>
            <div class="field">
                <label for="g_cat">Category *</label>
                <select id="g_cat" name="category">
                    <option value="Food" <?= strcasecmp($currentCat, 'Food') === 0 ? 'selected' : '' ?>>Food</option>
                    <option value="Interior" <?= strcasecmp($currentCat, 'Interior') === 0 ? 'selected' : '' ?>>Interior</option>
                    <option value="Events" <?= strcasecmp($currentCat, 'Events') === 0 ? 'selected' : '' ?>>Events</option>
                </select>
            </div>
        </div>
        <div class="field">
            <label for="g_title">Title</label>
            <input id="g_title" type="text" name="title" value="<?= e(old('title', (string) ($item['title'] ?? ''))) ?>">
        </div>
        <div class="field">
            <label for="g_caption">Caption</label>
            <input id="g_caption" type="text" name="caption" value="<?= e(old('caption', (string) ($item['caption'] ?? ''))) ?>">
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="g_alt">Alt Text</label>
                <input id="g_alt" type="text" name="alt_text" value="<?= e(old('alt_text', (string) ($item['alt_text'] ?? ''))) ?>">
            </div>
            <div class="field">
                <label for="g_sort">Sort Order</label>
                <input id="g_sort" type="number" min="0" name="sort_order" value="<?= e(old('sort_order', (string) ($item['sort_order'] ?? '0'))) ?>">
            </div>
        </div>
        <div class="field">
            <label for="g_image">Image File (for Photo type)</label>
            <input id="g_image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
            <small style="display:block;color:#57534e;margin-top:4px;">Recommended: 1280 × 720 px or 1200 × 800 px (min 800 × 533, 16:9/3:2 landscape, &lt; 250 KB). Auto-converted &amp; resized up to 1280 × 960 WebP.</small>
            <?php if ($isEdit && !empty($item['image_path'])): ?>
                <div style="margin-top:8px;"><img src="<?= e(media_url($item['image_path'])) ?>" alt="" class="thumb"></div>
            <?php endif; ?>
        </div>
        <div class="field">
            <label for="g_youtube">YouTube Video URL or ID (for YouTube type)</label>
            <input id="g_youtube" type="text" name="youtube_id" value="<?= e(old('youtube_id', (string) ($item['youtube_id'] ?? ''))) ?>" placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
            <small style="display:block;color:#57534e;margin-top:4px;">Supports watch, youtu.be, shorts, embed URLs, or 11-char ID. Upload 16:9 HD video (Public/Unlisted with embedding enabled) and a 1280 × 720 thumbnail in YouTube Studio.</small>
        </div>
        <div class="field">
            <label><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || !empty($item['is_active'])) ? 'checked' : '' ?>> Visible on Public Gallery</label>
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-primary">Save Gallery Item</button>
            <a href="<?= e(url('/admin/gallery')) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
