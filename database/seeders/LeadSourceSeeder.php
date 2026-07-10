<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'IndiaMART', 'color' => '#e74c3c', 'icon' => 'shop'],
            ['name' => 'Website', 'color' => '#3498db', 'icon' => 'globe'],
            ['name' => 'Facebook', 'color' => '#3b5998', 'icon' => 'facebook'],
            ['name' => 'Instagram', 'color' => '#e1306c', 'icon' => 'instagram'],
            ['name' => 'Google Ads', 'color' => '#4285f4', 'icon' => 'google'],
            ['name' => 'JustDial', 'color' => '#f39c12', 'icon' => 'telephone'],
            ['name' => 'TradeIndia', 'color' => '#2ecc71', 'icon' => 'building'],
            ['name' => 'Referral', 'color' => '#9b59b6', 'icon' => 'people'],
            ['name' => 'Manual', 'color' => '#95a5a6', 'icon' => 'pencil'],
            ['name' => 'CSV Import', 'color' => '#1abc9c', 'icon' => 'file-earmark-spreadsheet'],
            ['name' => 'API', 'color' => '#34495e', 'icon' => 'code-slash'],
        ];

        foreach ($sources as $index => $source) {
            DB::table('lead_sources')->updateOrInsert(
                ['slug' => Str::slug($source['name'])],
                [
                    'name' => $source['name'],
                    'color' => $source['color'],
                    'icon' => $source['icon'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command?->info('Lead sources seeded: '.count($sources));
    }
}
