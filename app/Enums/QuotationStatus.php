<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case Draft = 'Draft';
    case Sent = 'Sent';
    case Accepted = 'Accepted';
    case Rejected = 'Rejected';
    case Expired = 'Expired';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
