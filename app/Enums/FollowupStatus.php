<?php

namespace App\Enums;

enum FollowupStatus: string
{
    case Pending = 'Pending';
    case Completed = 'Completed';
    case Missed = 'Missed';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
