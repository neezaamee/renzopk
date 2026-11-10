<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\SliderService;

final class SliderController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderAdmin('admin/sliders/index', [
            'pageTitle' => 'Manage Home Sliders',
            'slides' => (new SliderService())->getAllAdmin(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->renderAdmin('admin/sliders/form', [
            'pageTitle' => 'Add Home Slide',
            'slide' => null,
            'actionUrl' => '/admin/sliders',
        ]);
    }

    public function store(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new SliderService())->save(null, $request->allPost(), $request->file('image')),
            '/admin/sliders',
            'Slider item added.',
            '/admin/sliders/create',
            $request
        );
    }

    public function edit(Request $request, string $id): Response
    {
        $slide = (new SliderService())->find((int) $id);
        if ($slide === null) {
            return Response::abort(404);
        }

        return $this->renderAdmin('admin/sliders/form', [
            'pageTitle' => 'Edit Home Slide',
            'slide' => $slide,
            'actionUrl' => '/admin/sliders/' . (int) $id,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $slideId = (int) $id;

        return $this->handleMutation(
            fn () => (new SliderService())->save($slideId, $request->allPost(), $request->file('image')),
            '/admin/sliders',
            'Slider item updated.',
            '/admin/sliders/' . $slideId . '/edit',
            $request
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new SliderService())->delete((int) $id),
            '/admin/sliders',
            'Slider item deleted.',
            '/admin/sliders',
            $request
        );
    }
}

