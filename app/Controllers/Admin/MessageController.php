<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AdminTableService;
use App\Services\ContactService;

final class MessageController extends Controller
{
    public function index(Request $request): Response
    {
        $filter = trim((string) $request->query('filter', ''));
        $paginated = (new ContactService())->paginateAdmin(
            $request->tableParams(AdminTableService::ALLOWED_PER_PAGE),
            $filter !== '' ? $filter : null
        );
        $table = (new AdminTableService())->buildMessagesTable($paginated, $filter);

        return $this->respondAdminTable($request, 'admin/messages/index', 'Contact & Career Messages', $table);
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

