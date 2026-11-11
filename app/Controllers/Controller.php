<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;
use App\Services\SettingsService;
use InvalidArgumentException;
use Throwable;

abstract class Controller
{
    protected function renderPublic(string $view, array $data = []): Response
    {
        $settings = (new SettingsService())->all();
        $html = View::render($view, array_merge($data, ['settings' => $settings]), 'layouts/main');

        return Response::html($html);
    }

    protected function renderAdmin(string $view, array $data = []): Response
    {
        $admin = (new AuthService())->currentAdmin();
        $settings = (new SettingsService())->all();

        $html = View::render($view, array_merge($data, [
            'currentAdmin' => $admin,
            'settings' => $settings,
        ]), 'layouts/admin');

        return Response::html($html);
    }

    protected function respondAdminTable(
        Request $request,
        string $view,
        string $pageTitle,
        array $tableConfig,
        array $extraData = []
    ): Response {
        if ($request->expectsJson()) {
            return Response::json([
                'table' => $tableConfig,
            ]);
        }

        return $this->renderAdmin($view, array_merge($extraData, [
            'pageTitle' => $pageTitle,
            'tableConfig' => $tableConfig,
        ]));
    }

    protected function renderAuth(string $view, array $data = []): Response
    {
        $settings = (new SettingsService())->all();
        $html = View::render($view, array_merge($data, ['settings' => $settings]), 'layouts/auth');

        return Response::html($html);
    }

    protected function redirectWithSuccess(string $path, string $message, string $flashScope = ''): Response
    {
        $key = $flashScope !== '' ? $flashScope . '_success' : 'success';
        Session::flash($key, $message);
        return Response::redirect($path);
    }

    protected function redirectWithError(string $path, string $message, array $oldInput = [], string $flashScope = ''): Response
    {
        $key = $flashScope !== '' ? $flashScope . '_error' : 'error';
        Session::flash($key, $message);
        if ($oldInput !== []) {
            Session::flashOld($oldInput);
        }

        return Response::redirect($path);
    }

    protected function safeRedirectPath(Request $request, string $fallback = '/', string $requiredPrefix = '/'): string
    {
        $raw = (string) $request->input('_redirect', $fallback);
        $clean = str_replace(["\r", "\n"], '', trim($raw));

        if ($clean === '' || !str_starts_with($clean, $requiredPrefix) || str_starts_with($clean, '//')) {
            return $fallback;
        }

        return $clean;
    }

    protected function handleMutation(
        callable $operation,
        string $successRedirect,
        string $successMessage,
        string $errorRedirect,
        Request $request,
        string $flashScope = ''
    ): Response {
        try {
            $operation();
            return $this->redirectWithSuccess($successRedirect, $successMessage, $flashScope);
        } catch (InvalidArgumentException $e) {
            return $this->redirectWithError($errorRedirect, $e->getMessage(), $request->allPost(), $flashScope);
        } catch (Throwable $e) {
            error_log('[Renzo] Action failed: ' . $e->getMessage());
            return $this->redirectWithError($errorRedirect, 'Unable to save changes: ' . $e->getMessage(), $request->allPost(), $flashScope);
        }
    }
}

