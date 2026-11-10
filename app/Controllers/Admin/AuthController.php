<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use Throwable;

final class AuthController extends Controller
{
    public function showLogin(Request $request): Response
    {
        if ((new AuthService())->currentAdmin() !== null) {
            return Response::redirect('/admin/dashboard');
        }

        return $this->renderAuth('admin/auth/login', [
            'pageTitle' => 'Admin Sign In',
        ]);
    }

    public function requestOtp(Request $request): Response
    {
        $email = (string) $request->input('email', '');

        try {
            $result = (new AuthService())->requestLoginOtp($email, $request->ip());
            Session::flash($result['ok'] ? 'success' : 'error', $result['message']);
            if (!$result['ok']) {
                Session::flashOld(['email' => $email]);
            }

            return Response::redirect($result['redirect']);
        } catch (Throwable $e) {
            error_log('[Renzo] OTP dispatch failed: ' . $e->getMessage());
            return $this->redirectWithError('/admin/login', 'Unable to send OTP email: ' . $e->getMessage(), ['email' => $email]);
        }
    }

    public function showOtp(Request $request): Response
    {
        $auth = new AuthService();
        if ($auth->currentAdmin() !== null) {
            return Response::redirect('/admin/dashboard');
        }

        $pendingEmail = $auth->pendingEmail();
        if ($pendingEmail === null) {
            return $this->redirectWithError('/admin/login', 'Please enter your admin email first.');
        }

        return $this->renderAuth('admin/auth/otp', [
            'pageTitle' => 'Verify Login Code',
            'pendingEmail' => $pendingEmail,
        ]);
    }

    public function verifyOtp(Request $request): Response
    {
        $auth = new AuthService();
        $email = (string) ($auth->pendingEmail() ?? $request->input('email', ''));
        $code = (string) $request->input('otp_code', '');

        if ($email === '') {
            return $this->redirectWithError('/admin/login', 'Login session expired. Please request a new code.');
        }

        $result = $auth->verifyLoginOtp($email, $code);
        if (!$result['ok']) {
            return $this->redirectWithError('/admin/verify', $result['message']);
        }

        return $this->redirectWithSuccess('/admin/dashboard', $result['message']);
    }

    public function logout(Request $request): Response
    {
        (new AuthService())->logout();
        return $this->redirectWithSuccess('/admin/login', 'You have been signed out.');
    }
}
