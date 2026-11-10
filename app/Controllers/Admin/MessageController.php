<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ContactService;

final class MessageController extends Controller
{
    public function index(Request $request): Response
    {
        $filter = trim((string) $request->query('filter', ''));

        return $this->renderAdmin('admin/messages/index', [
            'pageTitle' => 'Contact & Career Messages',
            'messages' => (new ContactService())->listAdmin($filter !== '' ? $filter : null),
            'selectedFilter' => $filter,
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        $message = (new ContactService())->viewAndMarkRead((int) $id);
        if ($message === null) {
            return Response::abort(404);
        }

        return $this->renderAdmin('admin/messages/show', [
            'pageTitle' => 'Message #' . (int) $id,
            'message' => $message,
        ]);
    }

    public function markRead(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new ContactService())->markRead((int) $id),
            '/admin/messages',
            'Message marked as read.',
            '/admin/messages',
            $request
        );
    }

    public function destroy(Request $request, string $id): Response
    {
        return $this->handleMutation(
            fn () => (new ContactService())->delete((int) $id),
            '/admin/messages',
            'Message deleted.',
            '/admin/messages',
            $request
        );
    }
}

