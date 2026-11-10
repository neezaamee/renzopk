<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\MenuPdfService;
use App\Services\MenuService;
use Throwable;

final class MenuController extends Controller
{
    private const DEFAULT_TAGS = [
        ['name' => 'Veg', 'slug' => 'veg'],
        ['name' => 'Non-Veg', 'slug' => 'non-veg'],
        ['name' => 'Vegan', 'slug' => 'vegan'],
        ['name' => 'Gluten-Free', 'slug' => 'gluten-free'],
        ['name' => 'Spicy', 'slug' => 'spicy'],
        ['name' => 'Contains Nuts', 'slug' => 'contains-nuts'],
    ];

    public function index(Request $request): Response
    {
        $categories = [];
        $tags = self::DEFAULT_TAGS;

        try {
            $menuService = new MenuService();
            $categories = $menuService->getPublicMenu();
            $dbTags = $menuService->getAllTags();
            if ($dbTags !== []) {
                $tags = $dbTags;
            }
        } catch (Throwable $e) {
            error_log('[Renzo] Menu index DB fallback: ' . $e->getMessage());
        }

        return $this->renderPublic('public/menu', [
            'pageTitle' => 'Menu',
            'metaDescription' => 'Browse Renzo’s full menu with dietary filters, portion options, and instant PDF menu download.',
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function pdf(Request $request): Response
    {
        $pdfPath = (new MenuPdfService())->ensureExists();

        return Response::file(
            $pdfPath,
            'application/pdf',
            true,
            'menu.pdf',
            'public, max-age=300, must-revalidate'
        );
    }
}
