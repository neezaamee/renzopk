<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\MenuService;

final class MenuCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderAdmin('admin/categories/index', [
            'pageTitle' => 'Menu Categories',
            'categories' => (new MenuService())->getAllCategoriesAdmin(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->renderAdmin('admin/categories/form', [
            'pageTitle' => 'New Menu Category',
            'category' => null,
            'actionUrl' => '/admin/menu/categories',
        ]);
    }

    public function store(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new MenuService())->saveCategory(null, $request->allPost()),
            '/admin/menu/categories',
            'Category created and Menu PDF updated.',
            '/admin/menu/categories/create',
            $request
        );
    }

    public function edit(Request $request, string $id): Response
    {
        $category = (new MenuService())->findCategory((int) $id);
        if ($category === null) {
            return Response::abort(404);
        }

        return $this->renderAdmin('admin/categories/form', [
            'pageTitle' => 'Edit Menu Category',
            'category' => $category,
            'actionUrl' => '/admin/menu/categories/' . (int) $id,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $catId = (int) $id;

        return $this->handleMutation(
            fn () => (new MenuService())->saveCategory($catId, $request->allPost()),
            '/admin/menu/categories',
            'Category updated and Menu PDF updated.',
            '/admin/menu/categories/' . $catId . '/edit',
            $request
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new MenuService())->deleteCategory((int) $id),
            '/admin/menu/categories',
            'Category deleted.',
            '/admin/menu/categories',
            $request
        );
    }
}

