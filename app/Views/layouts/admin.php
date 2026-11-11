<?php
$path = current_path();
$restaurantName = (string) ($settings['restaurant_name'] ?? 'Renzo');
$adminName = trim((string) ($currentAdmin['name'] ?? 'Admin'));
$initials = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $adminName) ?: 'A', 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e(($pageTitle ?? 'Admin') . ' — ' . $restaurantName . ' CMS') ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body>
<div class="admin-shell">
    <div class="sidebar-backdrop" data-admin-backdrop hidden></div>

    <aside id="admin-sidebar" class="sidebar" aria-label="CMS Sidebar Navigation">
        <div class="sidebar-head">
            <a href="<?= e(url('/admin/dashboard')) ?>" class="sidebar-brand">
                <span class="brand-mark"><?= e(substr($restaurantName, 0, 1) ?: 'R') ?></span>
                <span><?= e($restaurantName) ?> <small>CMS</small></span>
            </a>
            <button type="button" class="sidebar-close" data-admin-nav-close aria-label="Close menu">&times;</button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-group-label">Overview</div>
            <a href="<?= e(url('/admin/dashboard')) ?>" class="<?= $path === '/admin/dashboard' ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
                <span>Dashboard</span>
            </a>

            <div class="nav-group-label">Menu &amp; Offers</div>
            <a href="<?= e(url('/admin/menu')) ?>" class="<?= str_starts_with($path, '/admin/menu') && !str_contains($path, '/categories') && !str_contains($path, '/tags') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"></path></svg>
                <span>Menu Items</span>
            </a>
            <a href="<?= e(url('/admin/menu/categories')) ?>" class="<?= str_starts_with($path, '/admin/menu/categories') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16v12H4z"></path><path d="M4 10h16"></path></svg>
                <span>Categories</span>
            </a>
            <a href="<?= e(url('/admin/menu/tags')) ?>" class="<?= str_starts_with($path, '/admin/menu/tags') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                <span>Tags</span>
            </a>
            <a href="<?= e(url('/admin/deals')) ?>" class="<?= str_starts_with($path, '/admin/deals') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="12 2 15 8.5 22 9.3 17 14 18.2 21 12 17.5 5.8 21 7 14 2 9.3 9 8.5 12 2"></polygon></svg>
                <span>Deals &amp; Coupons</span>
            </a>

            <div class="nav-group-label">Media</div>
            <a href="<?= e(url('/admin/gallery')) ?>" class="<?= str_starts_with($path, '/admin/gallery') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                <span>Gallery</span>
            </a>
            <a href="<?= e(url('/admin/sliders')) ?>" class="<?= str_starts_with($path, '/admin/sliders') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M7 15h10"></path></svg>
                <span>Home Sliders</span>
            </a>

            <div class="nav-group-label">Guest Desk</div>
            <a href="<?= e(url('/admin/reservations')) ?>" class="<?= str_starts_with($path, '/admin/reservations') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Reservations</span>
            </a>
            <a href="<?= e(url('/admin/messages')) ?>" class="<?= str_starts_with($path, '/admin/messages') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <span>Messages</span>
            </a>

            <div class="nav-group-label">System</div>
            <a href="<?= e(url('/admin/settings')) ?>" class="<?= str_starts_with($path, '/admin/settings') ? 'active' : '' ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .66.39 1.26 1 1.51.16.07.33.1.51.1H21a2 2 0 1 1 0 4h-.09c-.18 0-.35.03-.51.1-.61.25-1 .85-1 1.51z"></path></svg>
                <span>Settings</span>
            </a>
            <?php if (($currentAdmin['role'] ?? '') === 'owner'): ?>
                <a href="<?= e(url('/admin/admins')) ?>" class="<?= str_starts_with($path, '/admin/admins') ? 'active' : '' ?>">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Admin Users</span>
                </a>
            <?php endif; ?>
            <a href="<?= e(url('/')) ?>" target="_blank" rel="noopener">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                <span>View Public Site</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <span class="user-avatar"><?= e($initials) ?></span>
                <div class="user-meta">
                    <div class="user-name"><?= e($adminName) ?></div>
                    <div class="user-role"><?= e(ucfirst((string) ($currentAdmin['role'] ?? 'staff'))) ?></div>
                </div>
            </div>
            <form method="POST" action="<?= e(url('/admin/logout')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-light btn-sm" style="width:100%;">Sign Out</button>
            </form>
        </div>
    </aside>

    <div class="admin-workspace">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button
                    type="button"
                    class="mobile-menu-btn"
                    data-admin-nav-open
                    aria-controls="admin-sidebar"
                    aria-expanded="false"
                    aria-label="Open navigation menu"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <h1 class="topbar-title"><?= e($pageTitle ?? 'Dashboard') ?></h1>
            </div>
            <div class="topbar-right">
                <a href="<?= e(url('/')) ?>" class="btn btn-secondary btn-sm" target="_blank" rel="noopener">Live Site &#8599;</a>
            </div>
        </header>

        <main class="admin-main">
            <?php require base_path('app/Views/partials/flash.php'); ?>
            <?= $content ?>
        </main>
    </div>
</div>
<script src="<?= e(asset('js/admin.js')) ?>" defer></script>
</body>
</html>
