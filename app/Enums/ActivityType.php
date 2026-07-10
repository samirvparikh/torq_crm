<?php

namespace App\Enums;

enum ActivityType: string
{
    case LeadCreated = 'Lead Created';
    case Assigned = 'Assigned';
    case Viewed = 'Viewed';
    case Edited = 'Edited';
    case Call = 'Call';
    case Email = 'Email';
    case WhatsApp = 'WhatsApp';
    case Quotation = 'Quotation';
    case StatusChanged = 'Status Changed';
    case CommentAdded = 'Comment Added';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
