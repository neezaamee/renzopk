<?php
$currency = (string) ($settings['currency_symbol'] ?? 'Rs. ');
$nonEmptyCategories = array_values(array_filter($categories, fn (array $c): bool => !empty($c['items'])));
?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h1 style="margin:0;">Our Menu</h1>
                <p class="muted" style="margin:4px 0 0;">Freshly prepared seasonal dishes from our kitchen.</p>
            </div>
            <a href="<?= e(url('/menu/pdf')) ?>" class="btn btn-gold" target="_blank" rel="noopener">Download PDF</a>
        </div>

        <div class="menu-filter-bar form-panel" data-menu-filter-root>
            <div class="menu-filter-top">
                <div class="field" style="margin-bottom:0;flex:1;min-width:220px;">
                    <label for="menu-search" style="margin-bottom:4px;">Search Menu</label>
                    <input id="menu-search" type="search" placeholder="Search dishes, flavors, ingredients..." data-menu-search>
                </div>
                <button type="button" class="btn btn-outline" data-menu-clear>Clear Filters</button>
            </div>

            <?php if (!empty($nonEmptyCategories)): ?>
                <div class="filter-group">
                    <span class="filter-label">Categories:</span>
                    <div class="filter-pills" style="margin-bottom:0;">
                        <button type="button" class="pill is-active" data-menu-cat="all">All</button>
                        <?php foreach ($nonEmptyCategories as $category): ?>
                            <button type="button" class="pill" data-menu-cat="<?= e($category['slug']) ?>">
                                <?= e($category['name']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($tags)): ?>
                <div class="filter-group">
                    <span class="filter-label">Dietary &amp; Tags:</span>
                    <div class="filter-pills" style="margin-bottom:0;">
                        <?php foreach ($tags as $tag): ?>
                            <button type="button" class="pill" data-menu-tag="<?= e(strtolower((string) $tag['slug'])) ?>" aria-pressed="false">
                                <?= e($tag['name']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-panel" id="menu-empty-state" style="display:<?= empty($nonEmptyCategories) ? 'block' : 'none' ?>;text-align:center;margin-top:20px;">
            <p class="muted" style="margin:0 0 12px;">No items match &mdash; try clearing filters.</p>
            <button type="button" class="btn btn-outline" data-menu-clear>Clear Filters</button>
        </div>

        <div id="menu-catalog" style="margin-top:24px;">
            <?php foreach ($nonEmptyCategories as $category): ?>
                <div class="menu-category" data-menu-category-section="<?= e($category['slug']) ?>">
                    <h2><?= e($category['name']) ?></h2>
                    <?php if (!empty($category['description'])): ?>
                        <p class="muted"><?= e($category['description']) ?></p>
                    <?php endif; ?>

                    <div class="menu-list">
                        <?php foreach ($category['items'] as $item): ?>
                            <?php
                            $tagSlugs = array_map(
                                fn (array $t): string => strtolower((string) $t['slug']),
                                $item['tags'] ?? []
                            );
                            $searchBlob = strtolower(trim(
                                $item['name'] . ' ' . ($item['description'] ?? '') . ' ' . $category['name'] . ' ' . implode(' ', $tagSlugs)
                            ));
                            ?>
                            <article
                                class="menu-row"
                                data-menu-item
                                data-category="<?= e($category['slug']) ?>"
                                data-tags="<?= e(implode(',', $tagSlugs)) ?>"
                                data-search="<?= e($searchBlob) ?>"
                            >
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="<?= e(media_url($item['image_path'])) ?>" alt="<?= e($item['name']) ?>" class="menu-thumb" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <div style="width:84px;flex-shrink:0;">
                                        <?php
                                        $placeholderClass = 'compact';
                                        $placeholderTag = 'Dish';
                                        $placeholderText = 'Photo coming soon';
                                        require base_path('app/Views/partials/photo_placeholder.php');
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <div style="flex:1;">
                                    <div style="display:flex;justify-content:space-between;gap:10px;align-items:baseline;">
                                        <h3 style="margin:0;font-size:1.1rem;"><?= e($item['name']) ?></h3>
                                        <?php if ($item['price'] !== null && $item['price'] !== ''): ?>
                                            <span class="price"><?= e(format_price($item['price'], $currency)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (empty($item['is_available'])): ?>
                                        <div style="font-size:0.82rem;color:#fca5a5;font-weight:600;">Currently Unavailable</div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['description'])): ?>
                                        <p class="muted" style="margin:6px 0 0;font-size:0.92rem;"><?= e($item['description']) ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($item['variants'])): ?>
                                        <ul class="variant-list">
                                            <?php foreach ($item['variants'] as $variant): ?>
                                                <li>
                                                    <?= e($variant['name']) ?> &mdash; <strong><?= e(format_price($variant['price'], $currency)) ?></strong>
                                                    <?php if (empty($variant['is_available'])): ?>
                                                        <span>(Unavailable)</span>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                    <?php if (!empty($item['tags'])): ?>
                                        <div class="badges">
                                            <?php foreach ($item['tags'] as $tag): ?>
                                                <span class="badge" <?= !empty($tag['color']) ? 'style="border-color:' . e($tag['color']) . ';"' : '' ?>>
                                                    <?= e($tag['name']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
