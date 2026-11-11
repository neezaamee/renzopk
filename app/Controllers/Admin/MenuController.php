<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AdminTableService;
use App\Services\MenuService;
use App\Services\SettingsService;

final class MenuController extends Controller
{
    public function index(Request $request): Response
    {
        $paginated = (new MenuService())->paginateItemsAdmin(
            $request->tableParams(AdminTableService::ALLOWED_PER_PAGE)
        );
        $currency = (string) ((new SettingsService())->all()['currency_symbol'] ?? 'Rs. ');
        $table = (new AdminTableService())->buildMenuItemsTable($paginated, $currency);

        return $this->respondAdminTable($request, 'admin/menu/index', 'Manage Menu Items', $table);
    }

    public function create(Request $request): Response
    {
        $menu = new MenuService();

        return $this->renderAdmin('admin/menu/form', [
            'pageTitle' => 'Add Menu Item',
            'item' => null,
            'categories' => $menu->getAllCategoriesAdmin(),
            'tags' => $menu->getAllTags(),
            'actionUrl' => '/admin/menu',
        ]);
    }

    public function store(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new MenuService())->saveItem(null, $request->allPost(), $request->file('image')),
            '/admin/menu',
            'Menu item created and Menu PDF updated.',
            '/admin/menu/create',
            $request
        );
    }

    public function edit(Request $request, string $id): Response
    {
        $menu = new MenuService();
        $item = $menu->findItemWithRelations((int) $id);
        if ($item === null) {
            return Response::abort(404);
        }

        return $this->renderAdmin('admin/menu/form', [
            'pageTitle' => 'Edit Menu Item',
            'item' => $item,
            'categories' => $menu->getAllCategoriesAdmin(),
            'tags' => $menu->getAllTags(),
            'actionUrl' => '/admin/menu/' . (int) $id,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $itemId = (int) $id;

        return $this->handleMutation(
            fn () => (new MenuService())->saveItem($itemId, $request->allPost(), $request->file('image')),
            '/admin/menu',
            'Menu item updated and Menu PDF regenerated.',
            '/admin/menu/' . $itemId . '/edit',
            $request
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new MenuService())->deleteItem((int) $id),
            '/admin/menu',
            'Menu item deleted and Menu PDF updated.',
            '/admin/menu',
            $request
        );
    }

    public function regeneratePdf(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new \App\Services\MenuPdfService())->regenerate(),
            '/admin/menu',
            'Menu PDF regenerated at /storage/pdfs/menu.pdf.',
            '/admin/menu',
            $request
        );
    }
}

