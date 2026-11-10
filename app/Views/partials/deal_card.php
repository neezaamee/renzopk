<?php
$typeLabels = [
    'permanent' => 'Permanent',
    'time_limited' => 'Limited Time',
    'coupon' => 'Coupon',
];
?>
<article class="card">
    <?php if (!empty($deal['image_path'])): ?>
        <img src="<?= e(media_url($deal['image_path'])) ?>" alt="<?= e($deal['title']) ?>" class="card-img" loading="lazy" decoding="async">
    <?php else: ?>
        <?php
        $placeholderClass = '';
        $placeholderTag = 'Special Offer';
        $placeholderText = 'Photo coming soon';
        require base_path('app/Views/partials/photo_placeholder.php');
        ?>
    <?php endif; ?>
    <div class="card-body">
        <div class="badges" style="margin-top:0;margin-bottom:8px;">
            <span class="badge"><?= e($typeLabels[$deal['type']] ?? ucwords(str_replace('_', ' ', (string) $deal['type']))) ?></span>
            <?php if (!empty($deal['discount_label'])): ?>
                <span class="badge"><?= e($deal['discount_label']) ?></span>
            <?php endif; ?>
        </div>
        <h3 class="card-title"><?= e($deal['title']) ?></h3>
        <?php if (!empty($deal['description'])): ?>
            <p class="muted" style="margin:6px 0;"><?= e($deal['description']) ?></p>
        <?php endif; ?>
        <?php if (!empty($deal['coupon_code'])): ?>
            <button type="button" class="coupon-chip" data-copy-code="<?= e($deal['coupon_code']) ?>" title="Tap to copy coupon code">
                <span class="coupon-chip-label">Code:</span>
                <strong><?= e($deal['coupon_code']) ?></strong>
                <span class="coupon-chip-status" data-copy-status>Tap to copy</span>
            </button>
        <?php endif; ?>
        <?php if (!empty($deal['end_datetime'])): ?>
            <p class="muted" style="font-size:0.85rem;margin-top:10px;margin-bottom:0;">
                Valid until <?= e(date('M j, Y g:i A', strtotime((string) $deal['end_datetime']))) ?>
            </p>
        <?php endif; ?>
    </div>
</article>
