<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\DealService;
use App\Services\MenuService;
use App\Services\SliderService;
use Throwable;

final class HomeController extends Controller
{
    private const DEFAULT_SLIDES = [
        [
            'title' => 'Opening soon',
            'subtitle' => 'Wood-fired seasonal dishes, warm hospitality, and curated evening pours in the heart of town.',
            'cta_text' => 'Explore Our Menu',
            'cta_url' => '/menu',
            'image_path' => null,
            'alt_text' => 'Renzo Opening Soon',
        ],
        [
            'title' => 'Opening soon — Come sit with us',
            'subtitle' => 'Intimate booths, hearth-side dining, and an atmosphere built for unhurried conversation.',
            'cta_text' => 'Reserve a Table',
            'cta_url' => '#contact',
            'image_path' => null,
            'alt_text' => 'Renzo Dining Room',
        ],
        [
            'title' => 'Opening soon — Crafted with care',
            'subtitle' => 'Every sauce, dough, and reduction prepared daily from scratch by our culinary team.',
            'cta_text' => 'View Current Deals',
            'cta_url' => '/deals',
            'image_path' => null,
            'alt_text' => 'Renzo Kitchen Craft',
        ],
    ];

    public function index(Request $request): Response
    {
        $slides = [];
        $menuPreview = [];
        $activeDeals = [];

        try {
            $slides = (new SliderService())->getActiveSlides();
            $menuPreview = (new MenuService())->getMenuPreview(3, 3);
            $activeDeals = (new DealService())->active(3);
        } catch (Throwable $e) {
            error_log('[Renzo] Home preview DB fallback: ' . $e->getMessage());
        }

        if ($slides === []) {
            $slides = self::DEFAULT_SLIDES;
        }

        $galleryTiles = [
            'Wood-Fired Hearth',
            'Artisan Plates',
            'Main Dining Room',
            'Evening Pours',
            "Chef's Counter",
            'Private Lounge',
        ];

        return $this->renderPublic('public/home', [
            'pageTitle' => 'Home',
            'metaDescription' => 'Renzo restaurant — wood-fired seasonal dining, signature menu, exclusive deals, and table reservations.',
            'slides' => $slides,
            'menuPreview' => $menuPreview,
            'activeDeals' => $activeDeals,
            'galleryTiles' => $galleryTiles,
        ]);
    }
}
