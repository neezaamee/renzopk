<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\Admin;

final class AuthService
{
    private const SESSION_ADMIN_ID = 'admin_id';
    private const SESSION_PENDING_EMAIL = '_otp_pending_email';

    private ?Admin $admins;
    private ?OtpService $otp;

    public function __construct(?Admin $admins = null, ?OtpService $otp = null)
    {
        $this->admins = $admins;
        $this->otp = $otp;
    }

    /**
     * @return array{ok: bool, redirect: string, message: string}
     */
    public function requestLoginOtp(string $emailInput, string $ipAddress): array
    {
        $email = strtolower(trim($emailInput));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'ok' => false,
                'redirect' => '/admin/login',
                'message' => 'Please provide a valid email address.',
            ];
        }

        $admin = $this->adminModel()->findActiveByEmail($email);
        if ($admin === null) {
            // Generic response avoids exposing valid admin emails
            Session::set(self::SESSION_PENDING_EMAIL, $email);
            return [
                'ok' => true,
                'redirect' => '/admin/verify',
                'message' => 'If an active admin account exists for that email, a 6-digit login code has been sent.',
            ];
        }

        $result = $this->otpService()->issue($admin, $ipAddress);
        if (!$result['ok']) {
            return [
                'ok' => false,
                'redirect' => '/admin/login',
                'message' => $result['message'],
            ];
        }

        Session::set(self::SESSION_PENDING_EMAIL, $email);

        return [
            'ok' => true,
            'redirect' => '/admin/verify',
            'message' => 'If an active admin account exists for that email, a 6-digit login code has been sent.',
        ];
    }

    public function pendingEmail(): ?string
    {
        $email = Session::get(self::SESSION_PENDING_EMAIL);
        return is_string($email) && $email !== '' ? $email : null;
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function verifyLoginOtp(string $emailInput, string $code): array
    {
        $email = strtolower(trim($emailInput));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'message' => 'Invalid email session. Please restart login.'];
        }

        $admin = $this->adminModel()->findActiveByEmail($email);
        if ($admin === null) {
            return ['ok' => false, 'message' => 'Invalid or expired verification code.'];
        }

        $verification = $this->otpService()->verify((int) $admin['id'], $code);
        if (!$verification['ok']) {
            return $verification;
        }

        Session::regenerate();
        CsrfService::rotate();
        Session::remove(self::SESSION_PENDING_EMAIL);
        Session::set(self::SESSION_ADMIN_ID, (int) $admin['id']);
        $this->adminModel()->touchLastLogin((int) $admin['id']);

        return ['ok' => true, 'message' => 'Welcome back, ' . $admin['name'] . '.'];
    }

    public function currentAdmin(): ?array
    {
        $adminId = Session::get(self::SESSION_ADMIN_ID);
        if ($adminId === null || (!is_int($adminId) && !ctype_digit((string) $adminId))) {
            return null;
        }

        $admin = $this->adminModel()->findActiveById((int) $adminId);
        if ($admin === null) {
            Session::remove(self::SESSION_ADMIN_ID);
            return null;
        }

        return $admin;
    }

    public function logout(): void
    {
        Session::remove(self::SESSION_ADMIN_ID);
        Session::remove(self::SESSION_PENDING_EMAIL);
        Session::regenerate();
        CsrfService::rotate();
    }

    private function adminModel(): Admin
    {
        return $this->admins ??= new Admin();
    }

    private function otpService(): OtpService
    {
        return $this->otp ??= new OtpService();
    }
}
