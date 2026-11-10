<?php
$path = current_path();
$waDigits = preg_replace('/\D+/', '', (string) ($settings['whatsapp_number'] ?? '923111200925')) ?: '923111200925';
$whatsappUrl = 'https://wa.me/' . $waDigits . '?text=Hi%20Renzo%2C%20';
$restaurantName = (string) ($settings['restaurant_name'] ?? 'Renzo');
$description = (string) ($metaDescription ?? ($settings['restaurant_tagline'] ?? 'Wood-Fired Kitchen & Contemporary Table'));
$baseHost = rtrim((string) \App\Core\Env::get('APP_URL', ''), '/');
if ($baseHost === '') {
    $isHttps = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    $baseHost = ($isHttps ? 'https://' : 'http://') . ((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
}
$canonicalUrl = $baseHost . url($path);

$hoursLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($settings['opening_hours'] ?? '')) ?: [])));
$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Restaurant',
    'name' => $restaurantName,
    'description' => $description,
    'url' => $baseHost . url('/'),
    'telephone' => (string) ($settings['contact_phone'] ?? ''),
    'email' => (string) ($settings['contact_email'] ?? ''),
    'servesCuisine' => 'Wood-Fired Contemporary',
    'hasMenu' => $baseHost . url('/menu'),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => (string) ($settings['contact_address'] ?? '124 Harbor Lane, Culinary District'),
    ],
    'openingHours' => $hoursLines,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= e(($pageTitle ?? 'Home') . ' — ' . $restaurantName) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e(($pageTitle ?? 'Home') . ' — ' . $restaurantName) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.min.css')) ?>">
    <script type="application/ld+json"><?= $jsonLd ?: '{}' ?></script>
</head>
<body>
<input type="checkbox" id="nav-toggle" class="nav-toggle-input" aria-hidden="true">

<header class="site-header">
    <div class="container header-inner">
        <a href="<?= e(url('/')) ?>" class="wordmark"><?= e($restaurantName) ?></a>

        <nav class="desktop-nav" aria-label="Primary Navigation">
            <a href="<?= e(url('/')) ?>" class="<?= $path === '/' ? 'active' : '' ?>">Home</a>
            <a href="<?= e(url('/menu')) ?>" class="<?= $path === '/menu' ? 'active' : '' ?>">Menu</a>
            <a href="<?= e(url('/deals')) ?>" class="<?= $path === '/deals' ? 'active' : '' ?>">Deals</a>
            <a href="<?= e(url('/gallery')) ?>" class="<?= $path === '/gallery' ? 'active' : '' ?>">Gallery</a>
            <a href="<?= e(url('/contact')) ?>" class="<?= $path === '/contact' ? 'active' : '' ?>">Contact</a>
            <a href="<?= e(url('/careers')) ?>" class="<?= $path === '/careers' ? 'active' : '' ?>">Careers</a>
            <a href="<?= e(url('/#reserve')) ?>" class="btn btn-gold">Reserve</a>
        </nav>

        <label for="nav-toggle" class="hamburger-btn" aria-label="Open navigation menu" role="button" tabindex="0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </label>
    </div>
</header>

<aside class="mobile-drawer" aria-label="Mobile Navigation">
    <div class="drawer-top">
        <span class="wordmark"><?= e($restaurantName) ?></span>
        <label for="nav-toggle" class="drawer-close" aria-label="Close navigation menu" role="button" tabindex="0">&times;</label>
    </div>
    <nav class="drawer-links">
        <a href="<?= e(url('/')) ?>" class="<?= $path === '/' ? 'active' : '' ?>">Home</a>
        <a href="<?= e(url('/menu')) ?>" class="<?= $path === '/menu' ? 'active' : '' ?>">Menu</a>
        <a href="<?= e(url('/deals')) ?>" class="<?= $path === '/deals' ? 'active' : '' ?>">Deals</a>
        <a href="<?= e(url('/gallery')) ?>" class="<?= $path === '/gallery' ? 'active' : '' ?>">Gallery</a>
        <a href="<?= e(url('/contact')) ?>" class="<?= $path === '/contact' ? 'active' : '' ?>">Contact</a>
        <a href="<?= e(url('/careers')) ?>" class="<?= $path === '/careers' ? 'active' : '' ?>">Careers</a>
        <a href="<?= e(url('/#reserve')) ?>" class="btn btn-gold" style="margin-top:8px;">Reserve</a>
    </nav>
    <div class="muted" style="font-size:0.9rem;">
        <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a>
    </div>
</aside>

<main>
    <div class="container">
        <?php require base_path('app/Views/partials/flash.php'); ?>
    </div>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h3 class="wordmark" style="font-size:1.35rem;"><?= e($restaurantName) ?></h3>
                <p><?= e($settings['restaurant_tagline'] ?? 'Wood-Fired Kitchen & Contemporary Table') ?></p>
                <div class="social-links">
                    <a href="<?= e(!empty($settings['social_instagram']) ? $settings['social_instagram'] : '#') ?>" rel="noopener">Instagram</a>
                    <a href="<?= e(!empty($settings['social_facebook']) ? $settings['social_facebook'] : '#') ?>" rel="noopener">Facebook</a>
                    <a href="<?= e(!empty($settings['social_tiktok']) ? $settings['social_tiktok'] : '#') ?>" rel="noopener">TikTok</a>
                    <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">WhatsApp</a>
                </div>
            </div>

            <div>
                <h4>Opening Hours</h4>
                <p style="white-space:pre-line;margin:0;"><?= e($settings['opening_hours'] ?? "Tue - Thu: 12:00 PM - 10:00 PM\nFri - Sun: 12:00 PM - 11:30 PM\nMon: Closed") ?></p>
                <p style="margin-top:10px;"><?= e($settings['contact_address'] ?? '') ?><br><?= e($settings['contact_phone'] ?? '') ?></p>
            </div>

            <div>
                <h4>Kitchen Preview</h4>
                <?php
                $placeholderClass = 'compact';
                $placeholderTag = 'Renzo Dining';
                $placeholderText = 'Photo coming soon';
                require base_path('app/Views/partials/photo_placeholder.php');
                ?>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?= e(date('Y')) ?> <?= e($restaurantName) ?>. All rights reserved.</span>
            <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">WhatsApp: +<?= e($waDigits) ?></a>
        </div>
    </div>
</footer>

<a href="<?= e($whatsappUrl) ?>" class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Chat with Renzo on WhatsApp">
    WhatsApp
</a>

<script src="<?= e(asset('js/app.min.js')) ?>" defer></script>
</body>
</html>
