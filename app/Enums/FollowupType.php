<?php

namespace App\Enums;

enum FollowupType: string
{
    case Call = 'Call';
    case Meeting = 'Meeting';
    case Email = 'Email';
    case WhatsApp = 'WhatsApp';
    case SMS = 'SMS';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
