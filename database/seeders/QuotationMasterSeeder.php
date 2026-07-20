<?php

namespace Database\Seeders;

use App\Models\QuotationTermTemplate;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationMasterSeeder extends Seeder
{
    public function run(): void
    {
        $companySettings = [
            ['group' => 'company', 'key' => 'name', 'value' => 'TORQ Packaging Solution', 'type' => 'string'],
            ['group' => 'company', 'key' => 'email', 'value' => 'info@torqpackagingsolution.com', 'type' => 'string'],
            ['group' => 'company', 'key' => 'phone', 'value' => '+91 80000 32128', 'type' => 'string'],
            ['group' => 'company', 'key' => 'website', 'value' => 'www.torqpackagingsolution.com', 'type' => 'string'],
            ['group' => 'company', 'key' => 'address', 'value' => '34, Vishala Estate, S.P. Ring Road, Opp. Odhav to Nikol Road, Nikol, Ahmedabad, Gujarat, 382350', 'type' => 'text'],
            ['group' => 'company', 'key' => 'gst_number', 'value' => '24ARDPD2565K1ZX', 'type' => 'string'],
            ['group' => 'company', 'key' => 'signatory_name', 'value' => 'Ronak Dave', 'type' => 'string'],
            ['group' => 'company', 'key' => 'signatory_phone', 'value' => '+91 80000 32128', 'type' => 'string'],
            ['group' => 'company', 'key' => 'intro_text', 'value' => 'Many thanks for your valuable inquiry of above subject. TORQ PACKAGING Solutions is engaged in making preliminary Bottle Packaging machines like bottle filling, capping, labeling, Automation, etc. in Ahmedabad. We are having dedicated team for manufacturing, customized machine development, Sales, servicing and back office.', 'type' => 'text'],
            ['group' => 'bank', 'key' => 'account_name', 'value' => 'TORQ PACKAGING SOLUTION', 'type' => 'string'],
            ['group' => 'bank', 'key' => 'account_number', 'value' => '8000032128', 'type' => 'string'],
            ['group' => 'bank', 'key' => 'bank_name', 'value' => 'KOTAK MAHINDRA BANK LTD', 'type' => 'string'],
            ['group' => 'bank', 'key' => 'branch', 'value' => 'KATHWADA', 'type' => 'string'],
            ['group' => 'bank', 'key' => 'ifsc', 'value' => 'KKBK0002606', 'type' => 'string'],
        ];

        foreach ($companySettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'is_encrypted' => false,
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        QuotationTermTemplate::query()->updateOrCreate(
            ['slug' => 'standard-machinery'],
            [
                'name' => 'Standard Machinery Terms',
                'content' => implode("\n", [
                    '1. PRICE — The prices mentioned are ex our Ahmedabad works.',
                    '2. TAXES & DUTIES — 18% GST extra as applicable (CGST+SGST or IGST).',
                    '3. TERMS OF PAYMENT — 60% Advance on Signing up of Contract; 40% On Completion of Machinery Manufacturing Before Dispatch / During Inspection of machinery.',
                    '4. DELIVERY — Delivery period will be confirmed after receipt of advance and approved drawings / samples.',
                    '5. PACKING & FORWARDING — Extra at actuals.',
                    '6. WARRANTY — 12 months from the date of dispatch against manufacturing defects (excluding consumables / wear & tear parts).',
                    '7. ORDER — Order once placed cannot be cancelled. No refund will be entertained.',
                    '8. JURISDICTION — All disputes subject to Ahmedabad jurisdiction only.',
                ]),
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        QuotationTermTemplate::query()->updateOrCreate(
            ['slug' => 'payment-only'],
            [
                'name' => 'Payment Terms Only',
                'content' => implode("\n", [
                    '60% Advance on Signing up of Contract',
                    '40% On Completion of Machinery Manufacturing Before Dispatch / During Inspection of machinery',
                    'Order Once Placed Cannot be Cancelled',
                    'No Refund will be entertained',
                ]),
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $this->command?->info('Quotation masters seeded (company, bank, terms).');
    }
}
