<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('indiamart_leads') && ! Schema::hasTable('indiamart_leads')) {
            Schema::rename('indiamart_leads', 'indiamart_leads');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('indiamart_leads') && ! Schema::hasTable('indiamart_leads')) {
            Schema::rename('indiamart_leads', 'indiamart_leads');
        }
    }
};
