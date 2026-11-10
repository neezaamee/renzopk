<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\GalleryService;
use Throwable;

final class GalleryController extends Controller
{
    private const CATEGORY_TABS = ['Food', 'Interior', 'Events'];

    public function index(Request $request): Response
    {
        $selectedCategory = trim((string) $request->query('category', ''));
        $matchedCategory = '';
        foreach (self::CATEGORY_TABS as $tab) {
            if (strcasecmp($selectedCategory, $tab) === 0) {
                $matchedCategory = $tab;
                break;
            }
        }

        $items = [];
        try {
            $items = (new GalleryService())->getPublicItems($matchedCategory !== '' ? $matchedCategory : null);
        } catch (Throwable $e) {
            error_log('[Renzo] Gallery index DB fallback: ' . $e->getMessage());
        }

        return $this->renderPublic('public/gallery', [
            'pageTitle' => 'Gallery',
            'metaDescription' => 'View photos and behind-the-scenes videos of Renzo’s dishes, interior, and private events.',
            'items' => $items,
            'categories' => self::CATEGORY_TABS,
            'selectedCategory' => $matchedCategory,
        ]);
    }
}
