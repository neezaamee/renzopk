<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\DealService;
use Throwable;

final class DealController extends Controller
{
    private const FILTER_TABS = [
        '' => 'All',
        'permanent' => 'Permanent',
        'time_limited' => 'Limited Time',
        'coupon' => 'Coupons',
    ];

    public function index(Request $request): Response
    {
        $selectedType = trim((string) $request->query('type', ''));
        if (!array_key_exists($selectedType, self::FILTER_TABS)) {
            $selectedType = '';
        }

        $deals = [];
        try {
            $deals = (new DealService())->active(null, $selectedType !== '' ? $selectedType : null);
        } catch (Throwable $e) {
            error_log('[Renzo] Deals index DB fallback: ' . $e->getMessage());
        }

        return $this->renderPublic('public/deals', [
            'pageTitle' => 'Special Deals',
            'metaDescription' => 'Explore Renzo’s active dining specials, limited-time offers, and exclusive coupon codes.',
            'deals' => $deals,
            'filterTabs' => self::FILTER_TABS,
            'selectedType' => $selectedType,
        ]);
    }
}
