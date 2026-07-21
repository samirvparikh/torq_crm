<?php

namespace App\Support;

use Illuminate\Support\Str;

final class LoginIdentifier
{
    /**
     * @return array{column: string, value: string}
     */
    public static function resolve(string $login): array
    {
        $login = trim($login);

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return ['column' => 'email', 'value' => Str::lower($login)];
        }

        $mobile = self::normalizeMobile($login);

        if ($mobile !== null) {
            return ['column' => 'mobile', 'value' => $mobile];
        }

        return ['column' => 'username', 'value' => Str::lower($login)];
    }

    public static function normalizeMobile(?string $mobile): ?string
    {
        if ($mobile === null || trim($mobile) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $mobile);

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return strlen($digits) === 10 ? $digits : null;
    }
}
