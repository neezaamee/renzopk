<?php $isEdit = is_array($slide); ?>
<div class="card" style="max-width:680px;">
    <form method="POST" action="<?= e(url($actionUrl)) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="field">
            <label for="s_title">Headline</label>
            <input id="s_title" type="text" name="title" value="<?= e(old('title', (string) ($slide['title'] ?? ''))) ?>">
        </div>
        <div class="field">
            <label for="s_sub">Subtitle</label>
            <input id="s_sub" type="text" name="subtitle" value="<?= e(old('subtitle', (string) ($slide['subtitle'] ?? ''))) ?>">
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="s_cta">Button Text</label>
                <input id="s_cta" type="text" name="cta_text" value="<?= e(old('cta_text', (string) ($slide['cta_text'] ?? ''))) ?>" placeholder="Explore Menu">
            </div>
            <div class="field">
                <label for="s_url">Button URL</label>
                <input id="s_url" type="text" name="cta_url" value="<?= e(old('cta_url', (string) ($slide['cta_url'] ?? ''))) ?>" placeholder="/menu">
            </div>
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="s_alt">Alt Text</label>
                <input id="s_alt" type="text" name="alt_text" value="<?= e(old('alt_text', (string) ($slide['alt_text'] ?? ''))) ?>">
            </div>
            <div class="field">
                <label for="s_sort">Sort Order</label>
                <input id="s_sort" type="number" min="0" name="sort_order" value="<?= e(old('sort_order', (string) ($slide['sort_order'] ?? '0'))) ?>">
            </div>
        </div>
        <div class="field">
            <label for="s_image">Banner Image (optional; dark-brown/gold gradient shown if empty)</label>
            <input id="s_image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
            <small style="display:block;color:#57534e;margin-top:4px;">Recommended: 1920 × 800 px (min 1440 × 600, 16:7–16:9 landscape, &lt; 350 KB, dark/centered subject). JPEG/PNG/WebP up to 5 MB; auto-converted &amp; resized up to 1920 × 1080 WebP.</small>
            <?php if ($isEdit && !empty($slide['image_path'])): ?>
                <div style="margin-top:8px;"><img src="<?= e(media_url($slide['image_path'])) ?>" alt="" class="thumb"></div>
            <?php endif; ?>
        </div>
        <div class="field">
            <label><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || !empty($slide['is_active'])) ? 'checked' : '' ?>> Active</label>
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-primary">Save Slide</button>
            <a href="<?= e(url('/admin/sliders')) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

