<?php

namespace App\Enums;

enum RoleName: string
{
    case SuperAdmin = 'Super Admin';
    case Admin = 'Admin';
    case Manager = 'Manager';
    case Marketing = 'Marketing';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Roles that can see all records (not assignee-scoped).
     *
     * @return list<string>
     */
    public static function unrestricted(): array
    {
        return [
            self::SuperAdmin->value,
            self::Admin->value,
            self::Manager->value,
        ];
    }

    /**
     * Roles allowed to see the Administration sidebar section.
     *
     * @return list<string>
     */
    public static function administration(): array
    {
        return [
            self::SuperAdmin->value,
            self::Admin->value,
        ];
    }
}
