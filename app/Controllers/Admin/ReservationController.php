<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Services\ReservationService;

final class ReservationController extends Controller
{
    public function index(Request $request): Response
    {
        $status = trim((string) $request->query('status', ''));

        return $this->renderAdmin('admin/reservations/index', [
            'pageTitle' => 'Reservations',
            'reservations' => (new ReservationService())->listAdmin($status !== '' ? $status : null),
            'selectedStatus' => $status,
            'statuses' => ReservationService::STATUSES,
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        $reservation = (new ReservationService())->findAdmin((int) $id);
        if ($reservation === null) {
            return Response::abort(404);
        }

        return $this->renderAdmin('admin/reservations/show', [
            'pageTitle' => 'Reservation #' . (int) $id,
            'reservation' => $reservation,
            'statuses' => ReservationService::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, string $id): Response
    {
        $resId = (int) $id;
        $admin = (new AuthService())->currentAdmin();
        $redirectTarget = $this->safeRedirectPath($request, '/admin/reservations/' . $resId, '/admin/');

        return $this->handleMutation(
            fn () => (new ReservationService())->updateStatus(
                $resId,
                (string) $request->input('status', ''),
                (string) $request->input('admin_notes', ''),
                (int) ($admin['id'] ?? 0)
            ),
            $redirectTarget,
            'Reservation status updated.',
            $redirectTarget,
            $request
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new ReservationService())->delete((int) $id),
            '/admin/reservations',
            'Reservation deleted.',
            '/admin/reservations',
            $request
        );
    }
}

