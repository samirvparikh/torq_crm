<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Industrial Equipment',
            'Electronics',
            'Machinery',
            'Raw Materials',
            'Packaging',
            'Services',
            'Others',
        ];

        foreach ($categories as $index => $name) {
            DB::table('categories')->updateOrInsert(
                ['slug' => Str::slug($name)],
                [
                    'parent_id' => null,
                    'name' => $name,
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command?->info('Categories seeded: '.count($categories));
    }
}
