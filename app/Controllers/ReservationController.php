<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\ReservationService;

final class ReservationController extends Controller
{
    public function store(Request $request): Response
    {
        $redirectTarget = $this->safeRedirectPath($request, '/#reserve');

        return $this->handleMutation(
            fn () => (new ReservationService())->submitPublic($request->allPost()),
            $redirectTarget,
            'Reservation request submitted! Our host team will confirm your table shortly.',
            $redirectTarget,
            $request,
            'reservation'
        );
    }
}
