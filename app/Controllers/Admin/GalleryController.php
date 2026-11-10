<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\GalleryService;

final class GalleryController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderAdmin('admin/gallery/index', [
            'pageTitle' => 'Manage Gallery',
            'items' => (new GalleryService())->getAllAdmin(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->renderAdmin('admin/gallery/form', [
            'pageTitle' => 'Upload Gallery Image',
            'item' => null,
            'actionUrl' => '/admin/gallery',
        ]);
    }

    public function store(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new GalleryService())->save(null, $request->allPost(), $request->file('image')),
            '/admin/gallery',
            'Gallery image added.',
            '/admin/gallery/create',
            $request
        );
    }

    public function edit(Request $request, string $id): Response
    {
        $item = (new GalleryService())->find((int) $id);
        if ($item === null) {
            return Response::abort(404);
        }

        return $this->renderAdmin('admin/gallery/form', [
            'pageTitle' => 'Edit Gallery Image',
            'item' => $item,
            'actionUrl' => '/admin/gallery/' . (int) $id,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $itemId = (int) $id;

        return $this->handleMutation(
            fn () => (new GalleryService())->save($itemId, $request->allPost(), $request->file('image')),
            '/admin/gallery',
            'Gallery image updated.',
            '/admin/gallery/' . $itemId . '/edit',
            $request
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new GalleryService())->delete((int) $id),
            '/admin/gallery',
            'Gallery image deleted.',
            '/admin/gallery',
            $request
        );
    }
}

