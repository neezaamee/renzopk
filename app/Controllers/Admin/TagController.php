<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\MenuService;

final class TagController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderAdmin('admin/tags/index', [
            'pageTitle' => 'Dietary & Dish Tags',
            'tags' => (new MenuService())->getAllTags(),
        ]);
    }

    public function store(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new MenuService())->createTag($request->allPost()),
            '/admin/menu/tags',
            'Tag added.',
            '/admin/menu/tags',
            $request
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new MenuService())->deleteTag((int) $id),
            '/admin/menu/tags',
            'Tag deleted.',
            '/admin/menu/tags',
            $request
        );
    }
}

