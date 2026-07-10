<?php

namespace App\Enums;

enum LeadPriority: string
{
    case Low = 'Low';
    case Medium = 'Medium';
    case High = 'High';
    case Urgent = 'Urgent';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'secondary',
            self::Medium => 'info',
            self::High => 'warning',
            self::Urgent => 'danger',
        };
    }
}
