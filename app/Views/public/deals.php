<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h1 style="margin:0;">Special Deals &amp; Offers</h1>
                <p class="muted" style="margin:4px 0 0;">Live dining specials, limited-time offers, and promo codes.</p>
            </div>
        </div>

        <div class="filter-pills">
            <?php foreach ($filterTabs as $typeKey => $label): ?>
                <?php $tabUrl = $typeKey === '' ? '/deals' : '/deals?type=' . urlencode($typeKey); ?>
                <a href="<?= e(url($tabUrl)) ?>" class="pill <?= ($selectedType ?? '') === $typeKey ? 'is-active' : '' ?>">
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($deals)): ?>
            <div class="form-panel">
                <p class="muted" style="margin:0;">No active deals in this category right now. Please check back soon!</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($deals as $deal): ?>
                    <?php require base_path('app/Views/partials/deal_card.php'); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
