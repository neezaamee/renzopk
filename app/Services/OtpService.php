<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OtpCode;

final class OtpService
{
    public const MAX_ATTEMPTS = 5;
    public const WINDOW_MINUTES = 10;
    public const EXPIRY_MINUTES = 10;

    private OtpCode $otpCodes;
    private MailService $mail;

    public function __construct(?OtpCode $otpCodes = null, ?MailService $mail = null)
    {
        $this->otpCodes = $otpCodes ?? new OtpCode();
        $this->mail = $mail ?? new MailService();
    }

    /**
     * @return array{ok: bool, rate_limited: bool, message: string}
     */
    public function issue(array $admin, string $ipAddress): array
    {
        $adminId = (int) $admin['id'];

        $recentRequests = $this->otpCodes->countRecentRequests($adminId, self::WINDOW_MINUTES);
        $recentAttempts = $this->otpCodes->sumRecentAttempts($adminId, self::WINDOW_MINUTES);

        if ($recentRequests >= self::MAX_ATTEMPTS || $recentAttempts >= self::MAX_ATTEMPTS) {
            return [
                'ok' => false,
                'rate_limited' => true,
                'message' => 'Too many OTP requests or verification attempts. Please wait 10 minutes before trying again.',
            ];
        }

        $plainCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $codeHash = password_hash($plainCode, PASSWORD_DEFAULT);

        $this->otpCodes->invalidateUnusedForAdmin($adminId);
        $this->otpCodes->createForAdmin($adminId, $codeHash, $ipAddress, self::EXPIRY_MINUTES);

        $this->mail->sendOtp(
            (string) $admin['email'],
            (string) $admin['name'],
            $plainCode,
            self::EXPIRY_MINUTES
        );

        return [
            'ok' => true,
            'rate_limited' => false,
            'message' => 'Verification code sent.',
        ];
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function verify(int $adminId, string $submittedCode): array
    {
        $cleanedCode = preg_replace('/\s+/', '', trim($submittedCode)) ?? '';
        if (!preg_match('/^\d{6}$/', $cleanedCode)) {
            return ['ok' => false, 'message' => 'Please enter a valid 6-digit verification code.'];
        }

        if ($this->otpCodes->sumRecentAttempts($adminId, self::WINDOW_MINUTES) >= self::MAX_ATTEMPTS) {
            return ['ok' => false, 'message' => 'Too many failed attempts. Please wait 10 minutes and request a new code.'];
        }

        $otp = $this->otpCodes->findLatestValidForAdmin($adminId);
        if ($otp === null) {
            return ['ok' => false, 'message' => 'Verification code is invalid or expired. Please request a new code.'];
        }

        $otpId = (int) $otp['id'];
        if (!$this->otpCodes->incrementAttemptIfAllowed($otpId, self::MAX_ATTEMPTS)) {
            return ['ok' => false, 'message' => 'Maximum verification attempts exceeded. Please wait 10 minutes.'];
        }

        if (!password_verify($cleanedCode, (string) $otp['code_hash'])) {
            $remaining = max(0, self::MAX_ATTEMPTS - ((int) $otp['attempts'] + 1));
            $suffix = $remaining > 0
                ? sprintf(' (%d attempt%s remaining)', $remaining, $remaining === 1 ? '' : 's')
                : ' Rate limit reached; wait 10 minutes.';

            return ['ok' => false, 'message' => 'Incorrect verification code.' . $suffix];
        }

        $this->otpCodes->markUsed($otpId);

        return ['ok' => true, 'message' => 'Verification succeeded.'];
    }
}

