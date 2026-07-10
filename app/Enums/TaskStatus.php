<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'Pending';
    case Completed = 'Completed';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
