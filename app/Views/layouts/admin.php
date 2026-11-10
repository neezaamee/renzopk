<?php $path = current_path(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($pageTitle ?? 'Admin') . ' — ' . ($settings['restaurant_name'] ?? 'Renzo') . ' CMS') ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <div class="sidebar-brand"><?= e($settings['restaurant_name'] ?? 'Renzo') ?> CMS</div>
        <a href="<?= e(url('/admin/dashboard')) ?>" class="<?= $path === '/admin/dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="<?= e(url('/admin/menu')) ?>" class="<?= str_starts_with($path, '/admin/menu') && !str_contains($path, '/categories') && !str_contains($path, '/tags') ? 'active' : '' ?>">Menu Items</a>
        <a href="<?= e(url('/admin/menu/categories')) ?>" class="<?= str_starts_with($path, '/admin/menu/categories') ? 'active' : '' ?>">Categories</a>
        <a href="<?= e(url('/admin/menu/tags')) ?>" class="<?= str_starts_with($path, '/admin/menu/tags') ? 'active' : '' ?>">Tags</a>
        <a href="<?= e(url('/admin/deals')) ?>" class="<?= str_starts_with($path, '/admin/deals') ? 'active' : '' ?>">Deals &amp; Coupons</a>
        <a href="<?= e(url('/admin/gallery')) ?>" class="<?= str_starts_with($path, '/admin/gallery') ? 'active' : '' ?>">Gallery</a>
        <a href="<?= e(url('/admin/sliders')) ?>" class="<?= str_starts_with($path, '/admin/sliders') ? 'active' : '' ?>">Home Sliders</a>
        <a href="<?= e(url('/admin/reservations')) ?>" class="<?= str_starts_with($path, '/admin/reservations') ? 'active' : '' ?>">Reservations</a>
        <a href="<?= e(url('/admin/messages')) ?>" class="<?= str_starts_with($path, '/admin/messages') ? 'active' : '' ?>">Messages</a>
        <a href="<?= e(url('/admin/settings')) ?>" class="<?= str_starts_with($path, '/admin/settings') ? 'active' : '' ?>">Settings</a>
        <?php if (($currentAdmin['role'] ?? '') === 'owner'): ?>
            <a href="<?= e(url('/admin/admins')) ?>" class="<?= str_starts_with($path, '/admin/admins') ? 'active' : '' ?>">Admin Users</a>
        <?php endif; ?>
        <a href="<?= e(url('/')) ?>" target="_blank" rel="noopener">View Public Site &#8599;</a>

        <div class="sidebar-footer">
            <div><strong><?= e($currentAdmin['name'] ?? '') ?></strong></div>
            <div style="color:#a8a29e;margin-bottom:10px;">Role: <?= e(ucfirst((string) ($currentAdmin['role'] ?? ''))) ?></div>
            <form method="POST" action="<?= e(url('/admin/logout')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary btn-sm">Sign Out</button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="topbar">
            <h1><?= e($pageTitle ?? 'Dashboard') ?></h1>
        </div>
        <?php require base_path('app/Views/partials/flash.php'); ?>
        <?= $content ?>
    </main>
</div>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>

