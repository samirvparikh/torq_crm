<?php

namespace App\Enums;

enum RoleName: string
{
    case SuperAdmin = 'Super Admin';
    case Admin = 'Admin';
    case SalesManager = 'Sales Manager';
    case SalesExecutive = 'Sales Executive';
    case TeleCaller = 'Tele Caller';
    case Marketing = 'Marketing';
    case Viewer = 'Viewer';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
