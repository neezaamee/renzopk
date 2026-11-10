<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\DashboardService;

final class DashboardController extends Controller
{
    public function redirect(Request $request): Response
    {
        return Response::redirect('/admin/dashboard');
    }

    public function index(Request $request): Response
    {
        $summary = (new DashboardService())->getSummary();

        return $this->renderAdmin('admin/dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'stats' => $summary['stats'],
            'recentReservations' => $summary['recent_reservations'],
            'recentMessages' => $summary['recent_messages'],
        ]);
    }
}

