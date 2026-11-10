<?php
$currency = (string) ($settings['currency_symbol'] ?? 'Rs. ');
$slideCount = count($slides);
?>

<section class="hero-slider" aria-label="Featured Highlights">
    <?php foreach ($slides as $idx => $slide): ?>
        <input type="radio" name="hero_slide" id="hero-slide-<?= e((string) ($idx + 1)) ?>" class="hero-radio">
    <?php endforeach; ?>

    <div class="hero-slides">
        <?php foreach ($slides as $idx => $slide): ?>
            <?php
            $bgIndex = ($idx % 3) + 1;
            $slideHeading = $slide['title'] ?? $slide['heading'] ?? ($settings['restaurant_name'] ?? 'Renzo');
            $ctaUrl = (string) ($slide['cta_url'] ?? '/menu');
            $ctaHref = (str_starts_with($ctaUrl, '#') || str_starts_with($ctaUrl, 'http')) ? $ctaUrl : url($ctaUrl);
            ?>
            <div class="hero-slide hero-slide-bg-<?= e((string) $bgIndex) ?>">
                <?php if (!empty($slide['image_path'])): ?>
                    <img
                        src="<?= e(media_url($slide['image_path'])) ?>"
                        alt="<?= e($slide['alt_text'] ?: $slideHeading) ?>"
                        class="hero-slide-img"
                        <?= $idx === 0 ? 'fetchpriority="high" decoding="async"' : 'loading="lazy" decoding="async"' ?>
                    >
                <?php endif; ?>
                <div class="container">
                    <div class="hero-content">
                        <span class="hero-eyebrow"><?= e($settings['restaurant_name'] ?? 'Renzo') ?></span>
                        <h1><?= e($slideHeading) ?></h1>
                        <?php if (!empty($slide['subtitle'])): ?>
                            <p><?= e($slide['subtitle']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($slide['cta_text'])): ?>
                            <a href="<?= e($ctaHref) ?>" class="btn btn-gold">
                                <?= e($slide['cta_text']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($slideCount > 1): ?>
        <div class="hero-dots" aria-label="Slide controls">
            <?php foreach ($slides as $idx => $slide): ?>
                <label for="hero-slide-<?= e((string) ($idx + 1)) ?>" class="hero-dot" aria-label="Slide <?= e((string) ($idx + 1)) ?>"></label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Menu Preview</h2>
            <a href="<?= e(url('/menu')) ?>">Explore Full Menu &rarr;</a>
        </div>

        <?php if (empty($menuPreview)): ?>
            <div class="form-panel">
                <p class="muted" style="margin:0;">Seasonal dishes are being prepared in our kitchen. View our full <a href="<?= e(url('/menu')) ?>">Menu</a> page shortly.</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($menuPreview as $category): ?>
                    <article class="card">
                        <div class="card-body">
                            <h3 class="card-title" style="color:var(--gold);"><?= e($category['name']) ?></h3>
                            <?php if (!empty($category['description'])): ?>
                                <p class="muted" style="font-size:0.88rem;margin-top:0;"><?= e($category['description']) ?></p>
                            <?php endif; ?>

                            <?php if (empty($category['items'])): ?>
                                <p class="muted" style="font-size:0.9rem;margin:0;">Items coming soon.</p>
                            <?php else: ?>
                                <ul class="variant-list" style="margin-top:12px;">
                                    <?php foreach ($category['items'] as $item): ?>
                                        <li style="padding:10px 0;border-top:1px solid var(--border);">
                                            <div style="display:flex;justify-content:space-between;gap:8px;align-items:baseline;">
                                                <strong style="color:var(--ink);"><?= e($item['name']) ?></strong>
                                                <?php if ($item['price'] !== null && $item['price'] !== ''): ?>
                                                    <span class="price"><?= e(format_price($item['price'], $currency)) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($item['description'])): ?>
                                                <div class="muted" style="font-size:0.84rem;margin-top:3px;"><?= e($item['description']) ?></div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Featured Deals</h2>
            <a href="<?= e(url('/deals')) ?>">All Active Deals &rarr;</a>
        </div>

        <?php if (empty($activeDeals)): ?>
            <div class="form-panel">
                <p class="muted" style="margin:0;">No active specials right now. Check back soon or ask your server about tonight's pairings.</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($activeDeals as $deal): ?>
                    <?php require base_path('app/Views/partials/deal_card.php'); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Inside Renzo</h2>
            <a href="<?= e(url('/gallery')) ?>">View Gallery &rarr;</a>
        </div>
        <div class="grid-3">
            <?php foreach ($galleryTiles as $tileTitle): ?>
                <?php
                $placeholderClass = 'tile';
                $placeholderTag = $tileTitle;
                $placeholderText = 'Photo coming soon';
                require base_path('app/Views/partials/photo_placeholder.php');
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt" id="reserve">
    <div class="container" style="max-width:860px;">
        <div class="section-header">
            <h2>Reserve a Table</h2>
            <span class="muted">Instant request &mdash; confirmed by our host team</span>
        </div>
        <?php $redirectPath = '/#reserve'; require base_path('app/Views/partials/reservation_form.php'); ?>
    </div>
</section>

<section class="section" id="contact">
    <div class="container">
        <div class="section-header">
            <h2>Visit &amp; Contact Us</h2>
            <a href="<?= e(url('/contact')) ?>">Full Hours &amp; Details &rarr;</a>
        </div>

        <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <div>
                <div class="form-panel" style="margin-bottom:20px;">
                    <h3 style="margin-top:0;color:var(--gold);">Hours &amp; Location</h3>
                    <p style="white-space:pre-line;margin-top:0;"><?= e($settings['opening_hours'] ?? "Tue - Thu: 12:00 PM - 10:00 PM\nFri - Sun: 12:00 PM - 11:30 PM\nMon: Closed") ?></p>
                    <p style="margin-bottom:0;" class="muted">
                        <?= e($settings['contact_address'] ?? '124 Harbor Lane, Culinary District') ?><br>
                        <?= e($settings['contact_phone'] ?? '+92 311 1200925') ?>
                    </p>
                </div>
                <?php require base_path('app/Views/partials/map_embed.php'); ?>
            </div>

            <div class="form-panel">
                <h3 style="margin-top:0;color:var(--gold);">Send a Message or Inquiry</h3>
                <?php $flashPrefix = 'contact_'; require base_path('app/Views/partials/flash.php'); ?>
                <form method="POST" action="<?= e(url('/contact')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_redirect" value="/#contact">
                    <div class="hp-field" aria-hidden="true">
                        <label for="home_hp_website">Website</label>
                        <input id="home_hp_website" type="text" name="_hp_website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="home_c_name">Name *</label>
                            <input id="home_c_name" type="text" name="name" value="<?= e(old('name')) ?>" required>
                        </div>
                        <div class="field">
                            <label for="home_c_email">Email *</label>
                            <input id="home_c_email" type="email" name="email" value="<?= e(old('email')) ?>" required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="home_c_phone">Phone</label>
                        <input id="home_c_phone" type="text" name="phone" value="<?= e(old('phone')) ?>">
                    </div>
                    <div class="field">
                        <label for="home_c_message">Message *</label>
                        <textarea id="home_c_message" name="message" rows="4" required><?= e(old('message')) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>
