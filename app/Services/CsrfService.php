<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;

final class CsrfService
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        $token = Session::get(self::SESSION_KEY);
        if (!is_string($token) || strlen($token) !== 64) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public static function verify(string $submitted): bool
    {
        $stored = Session::get(self::SESSION_KEY);
        if (!is_string($stored) || $stored === '' || $submitted === '') {
            return false;
        }

        return hash_equals($stored, $submitted);
    }

    public static function rotate(): void
    {
        Session::set(self::SESSION_KEY, bin2hex(random_bytes(32)));
    }
}

