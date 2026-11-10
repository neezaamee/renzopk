<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AdminUserService;
use App\Services\AuthService;

final class AdminUserController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderAdmin('admin/admins/index', [
            'pageTitle' => 'Admin Accounts',
            'admins' => (new AdminUserService())->listAll(),
        ]);
    }

    public function store(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new AdminUserService())->create($request->allPost()),
            '/admin/admins',
            'Admin user added. They can now sign in with email OTP.',
            '/admin/admins',
            $request
        );
    }

    public function toggle(Request $request, string $id): Response
    {
        $currentAdmin = (new AuthService())->currentAdmin();

        return $this->handleMutation(
            fn () => (new AdminUserService())->toggleActive((int) $id, (int) ($currentAdmin['id'] ?? 0)),
            '/admin/admins',
            'Admin status updated.',
            '/admin/admins',
            $request
        );
    }
}

