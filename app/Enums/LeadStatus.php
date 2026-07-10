<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'New';
    case Assigned = 'Assigned';
    case Contacted = 'Contacted';
    case Interested = 'Interested';
    case FollowUp = 'Follow Up';
    case QuotationSent = 'Quotation Sent';
    case Negotiation = 'Negotiation';
    case Won = 'Won';
    case Lost = 'Lost';
    case Junk = 'Junk';
    case Duplicate = 'Duplicate';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::Won, self::Lost, self::Junk, self::Duplicate], true);
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'primary',
            self::Assigned => 'info',
            self::Contacted => 'secondary',
            self::Interested => 'success',
            self::FollowUp => 'warning',
            self::QuotationSent => 'info',
            self::Negotiation => 'warning',
            self::Won => 'success',
            self::Lost => 'danger',
            self::Junk => 'dark',
            self::Duplicate => 'secondary',
        };
    }
}
