<?php

declare(strict_types=1);

use App\Controllers\Admin\AdminUserController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\DealController as AdminDealController;
use App\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Controllers\Admin\MenuCategoryController;
use App\Controllers\Admin\MenuController as AdminMenuController;
use App\Controllers\Admin\MessageController;
use App\Controllers\Admin\ReservationController as AdminReservationController;
use App\Controllers\Admin\SettingController;
use App\Controllers\Admin\SliderController;
use App\Controllers\Admin\TagController;
use App\Controllers\CareerController;
use App\Controllers\ContactController;
use App\Controllers\DealController;
use App\Controllers\GalleryController;
use App\Controllers\HomeController;
use App\Controllers\MediaController;
use App\Controllers\MenuController;
use App\Controllers\ReservationController;
use App\Controllers\SeoController;

return [
    // Public Website Routes
    ['GET',  '/',             [HomeController::class, 'index'],        ['name' => 'home']],
    ['GET',  '/menu',         [MenuController::class, 'index'],        ['name' => 'menu.index']],
    ['GET',  '/menu/pdf',     [MenuController::class, 'pdf'],          ['name' => 'menu.pdf']],
    ['GET',  '/deals',        [DealController::class, 'index'],        ['name' => 'deals.index']],
    ['GET',  '/gallery',      [GalleryController::class, 'index'],     ['name' => 'gallery.index']],
    ['GET',  '/contact',      [ContactController::class, 'index'],     ['name' => 'contact.index']],
    ['POST', '/contact',      [ContactController::class, 'store'],     ['name' => 'contact.store']],
    ['GET',  '/careers',      [CareerController::class, 'index'],      ['name' => 'careers.index']],
    ['POST', '/careers',      [CareerController::class, 'store'],      ['name' => 'careers.store']],
    ['POST', '/reservations', [ReservationController::class, 'store'], ['name' => 'reservations.store']],
    ['GET',  '/sitemap.xml',  [SeoController::class, 'sitemap'],       ['name' => 'seo.sitemap']],
    ['GET',  '/robots.txt',   [SeoController::class, 'robots'],        ['name' => 'seo.robots']],

    // Safe uploaded media streaming from /storage/uploads
    ['GET',  '/media/{folder}/{file}', [MediaController::class, 'show'], ['name' => 'media.show']],

    // Admin Authentication (OTP Flow)
    ['GET',  '/admin/login',  [AuthController::class, 'showLogin'],  ['name' => 'admin.login']],
    ['POST', '/admin/login',  [AuthController::class, 'requestOtp'], ['name' => 'admin.login.post']],
    ['GET',  '/admin/verify', [AuthController::class, 'showOtp'],    ['name' => 'admin.verify']],
    ['POST', '/admin/verify', [AuthController::class, 'verifyOtp'],  ['name' => 'admin.verify.post']],
    ['GET',  '/admin/otp',    [AuthController::class, 'showOtp'],    ['name' => 'admin.otp']],
    ['POST', '/admin/otp',    [AuthController::class, 'verifyOtp'],  ['name' => 'admin.otp.verify']],
    ['POST', '/admin/logout', [AuthController::class, 'logout'],     ['name' => 'admin.logout', 'middleware' => ['auth:admin']]],

    // Admin Dashboard
    ['GET',  '/admin',           [DashboardController::class, 'redirect'], ['name' => 'admin.root', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/dashboard', [DashboardController::class, 'index'],    ['name' => 'admin.dashboard', 'middleware' => ['auth:admin']]],

    // Admin Menu Categories
    ['GET',  '/admin/menu/categories',             [MenuCategoryController::class, 'index'],   ['name' => 'admin.categories.index', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/menu/categories/create',      [MenuCategoryController::class, 'create'],  ['name' => 'admin.categories.create', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu/categories',             [MenuCategoryController::class, 'store'],   ['name' => 'admin.categories.store', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/menu/categories/{id}/edit',   [MenuCategoryController::class, 'edit'],    ['name' => 'admin.categories.edit', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu/categories/{id}',        [MenuCategoryController::class, 'update'],  ['name' => 'admin.categories.update', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu/categories/{id}/delete', [MenuCategoryController::class, 'destroy'], ['name' => 'admin.categories.destroy', 'middleware' => ['auth:admin']]],

    // Admin Menu Tags
    ['GET',  '/admin/menu/tags',             [TagController::class, 'index'],   ['name' => 'admin.tags.index', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu/tags',             [TagController::class, 'store'],   ['name' => 'admin.tags.store', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu/tags/{id}/delete', [TagController::class, 'destroy'], ['name' => 'admin.tags.destroy', 'middleware' => ['auth:admin']]],

    // Admin Menu Items (including variants, tags & PDF regeneration)
    ['GET',  '/admin/menu',                [AdminMenuController::class, 'index'],         ['name' => 'admin.menu.index', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/menu/create',         [AdminMenuController::class, 'create'],        ['name' => 'admin.menu.create', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu',                [AdminMenuController::class, 'store'],         ['name' => 'admin.menu.store', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu/regenerate-pdf', [AdminMenuController::class, 'regeneratePdf'], ['name' => 'admin.menu.pdf', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/menu/{id}/edit',   [AdminMenuController::class, 'edit'],    ['name' => 'admin.menu.edit', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu/{id}',        [AdminMenuController::class, 'update'],  ['name' => 'admin.menu.update', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/menu/{id}/delete', [AdminMenuController::class, 'destroy'], ['name' => 'admin.menu.destroy', 'middleware' => ['auth:admin']]],

    // Admin Deals
    ['GET',  '/admin/deals',             [AdminDealController::class, 'index'],   ['name' => 'admin.deals.index', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/deals/create',      [AdminDealController::class, 'create'],  ['name' => 'admin.deals.create', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/deals',             [AdminDealController::class, 'store'],   ['name' => 'admin.deals.store', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/deals/{id}/edit',   [AdminDealController::class, 'edit'],    ['name' => 'admin.deals.edit', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/deals/{id}',        [AdminDealController::class, 'update'],  ['name' => 'admin.deals.update', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/deals/{id}/delete', [AdminDealController::class, 'destroy'], ['name' => 'admin.deals.destroy', 'middleware' => ['auth:admin']]],

    // Admin Gallery
    ['GET',  '/admin/gallery',             [AdminGalleryController::class, 'index'],   ['name' => 'admin.gallery.index', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/gallery/create',      [AdminGalleryController::class, 'create'],  ['name' => 'admin.gallery.create', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/gallery',             [AdminGalleryController::class, 'store'],   ['name' => 'admin.gallery.store', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/gallery/{id}/edit',   [AdminGalleryController::class, 'edit'],    ['name' => 'admin.gallery.edit', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/gallery/{id}',        [AdminGalleryController::class, 'update'],  ['name' => 'admin.gallery.update', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/gallery/{id}/delete', [AdminGalleryController::class, 'destroy'], ['name' => 'admin.gallery.destroy', 'middleware' => ['auth:admin']]],

    // Admin Home Sliders
    ['GET',  '/admin/sliders',             [SliderController::class, 'index'],   ['name' => 'admin.sliders.index', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/sliders/create',      [SliderController::class, 'create'],  ['name' => 'admin.sliders.create', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/sliders',             [SliderController::class, 'store'],   ['name' => 'admin.sliders.store', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/sliders/{id}/edit',   [SliderController::class, 'edit'],    ['name' => 'admin.sliders.edit', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/sliders/{id}',        [SliderController::class, 'update'],  ['name' => 'admin.sliders.update', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/sliders/{id}/delete', [SliderController::class, 'destroy'], ['name' => 'admin.sliders.destroy', 'middleware' => ['auth:admin']]],

    // Admin Reservations
    ['GET',  '/admin/reservations',             [AdminReservationController::class, 'index'],        ['name' => 'admin.reservations.index', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/reservations/{id}',        [AdminReservationController::class, 'show'],         ['name' => 'admin.reservations.show', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/reservations/{id}/status', [AdminReservationController::class, 'updateStatus'], ['name' => 'admin.reservations.status', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/reservations/{id}/delete', [AdminReservationController::class, 'destroy'],      ['name' => 'admin.reservations.destroy', 'middleware' => ['auth:admin']]],

    // Admin Contact Messages
    ['GET',  '/admin/messages',             [MessageController::class, 'index'],    ['name' => 'admin.messages.index', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/messages/{id}',        [MessageController::class, 'show'],     ['name' => 'admin.messages.show', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/messages/{id}/read',   [MessageController::class, 'markRead'], ['name' => 'admin.messages.read', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/messages/{id}/delete', [MessageController::class, 'destroy'],  ['name' => 'admin.messages.destroy', 'middleware' => ['auth:admin']]],

    // Admin Settings & Owner User Management
    ['GET',  '/admin/settings',           [SettingController::class, 'index'],   ['name' => 'admin.settings.index', 'middleware' => ['auth:admin']]],
    ['POST', '/admin/settings',           [SettingController::class, 'update'],  ['name' => 'admin.settings.update', 'middleware' => ['auth:admin']]],
    ['GET',  '/admin/admins',             [AdminUserController::class, 'index'], ['name' => 'admin.admins.index', 'middleware' => ['auth:admin', 'role:owner']]],
    ['POST', '/admin/admins',             [AdminUserController::class, 'store'], ['name' => 'admin.admins.store', 'middleware' => ['auth:admin', 'role:owner']]],
    ['POST', '/admin/admins/{id}/toggle', [AdminUserController::class, 'toggle'],['name' => 'admin.admins.toggle', 'middleware' => ['auth:admin', 'role:owner']]],
];

