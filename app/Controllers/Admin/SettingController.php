<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Services\SettingsService;

final class SettingController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderAdmin('admin/settings/index', [
            'pageTitle' => 'Restaurant Settings',
            'allSettings' => (new SettingsService())->all(),
        ]);
    }

    public function update(Request $request): Response
    {
        $admin = (new AuthService())->currentAdmin();

        return $this->handleMutation(
            fn () => (new SettingsService())->updateFromInput($request->allPost(), (int) ($admin['id'] ?? 0)),
            '/admin/settings',
            'Settings updated.',
            '/admin/settings',
            $request
        );
    }
}

