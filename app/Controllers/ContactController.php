<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\ContactService;

final class ContactController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderPublic('public/contact', [
            'pageTitle' => 'Contact & Reservations',
            'metaDescription' => 'Contact Renzo for dining inquiries, private events, opening hours, and directions.',
        ]);
    }

    public function store(Request $request): Response
    {
        $redirectTarget = $this->safeRedirectPath($request, '/contact#contact-form');

        return $this->handleMutation(
            fn () => (new ContactService())->submit($request->allPost(), $request->ip(), 'general'),
            $redirectTarget,
            'Thank you! Your message has been received and our team will reply shortly.',
            $redirectTarget,
            $request,
            'contact'
        );
    }
}
