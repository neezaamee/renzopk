<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\ContactService;

final class CareerController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderPublic('public/careers', [
            'pageTitle' => 'Careers',
            'metaDescription' => 'Join the culinary and hospitality team at Renzo. Send your CV directly to our hiring team.',
        ]);
    }

    public function store(Request $request): Response
    {
        return $this->handleMutation(
            fn () => (new ContactService())->submit($request->allPost(), $request->ip(), 'career'),
            '/careers',
            'Thank you for your interest in joining Renzo! Our management team will review your inquiry.',
            '/careers',
            $request
        );
    }
}

