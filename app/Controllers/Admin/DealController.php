<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\DealService;

final class DealController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderAdmin('admin/deals/index', [
            'pageTitle' => 'Manage Deals & Coupons',
            'deals' => (new DealService())->getAdminDealsWithStatus(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->renderAdmin('admin/deals/form', [
            'pageTitle' => 'Create Deal',
            'deal' => null,
            'actionUrl' => '/admin/deals',
        ]);
    }

    public function store(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new DealService())->save(null, $request->allPost(), $request->file('image')),
            '/admin/deals',
            'Deal created.',
            '/admin/deals/create',
            $request
        );
    }

    public function edit(Request $request, string $id): Response
    {
        $deal = (new DealService())->find((int) $id);
        if ($deal === null) {
            return Response::abort(404);
        }

        return $this->renderAdmin('admin/deals/form', [
            'pageTitle' => 'Edit Deal',
            'deal' => $deal,
            'actionUrl' => '/admin/deals/' . (int) $id,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $dealId = (int) $id;

        return $this->handleMutation(
            fn () => (new DealService())->save($dealId, $request->allPost(), $request->file('image')),
            '/admin/deals',
            'Deal updated.',
            '/admin/deals/' . $dealId . '/edit',
            $request
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new DealService())->delete((int) $id),
            '/admin/deals',
            'Deal deleted.',
            '/admin/deals',
            $request
        );
    }
}

