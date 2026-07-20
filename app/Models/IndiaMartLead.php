<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndiaMartLead extends Model
{
    protected $table = 'indiamart_leads';

    public $timestamps = false;

    protected $fillable = [
        'unique_query_id',
        'query_type',
        'query_time',
        'sender_name',
        'sender_mobile',
        'sender_email',
        'sender_company',
        'sender_address',
        'sender_city',
        'sender_state',
        'sender_pincode',
        'sender_country_iso',
        'sender_mobile_alt',
        'sender_email_alt',
        'query_product_name',
        'query_message',
        'query_mcat_name',
        'call_duration',
        'receiver_mobile',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'query_time' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
