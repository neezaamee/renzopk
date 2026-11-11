<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AdminTableService;
use App\Services\MenuService;

final class TagController extends Controller
{
    public function index(Request $request): Response
    {
        $paginated = (new MenuService())->paginateTagsAdmin(
            $request->tableParams(AdminTableService::ALLOWED_PER_PAGE)
        );
        $table = (new AdminTableService())->buildTagsTable($paginated);

        return $this->respondAdminTable($request, 'admin/tags/index', 'Dietary & Dish Tags', $table);
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

