<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'query_type')) {
                $table->string('query_type', 10)->nullable()->after('indiamart_lead_id');
            }
            if (! Schema::hasColumn('leads', 'query_time')) {
                $table->dateTime('query_time')->nullable()->after('query_type');
            }
            if (! Schema::hasColumn('leads', 'query_mcat_name')) {
                $table->string('query_mcat_name', 255)->nullable()->after('query_time');
            }
            if (! Schema::hasColumn('leads', 'call_duration')) {
                $table->string('call_duration', 50)->nullable()->after('query_mcat_name');
            }
            if (! Schema::hasColumn('leads', 'receiver_mobile')) {
                $table->string('receiver_mobile', 30)->nullable()->after('call_duration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            foreach (['query_type', 'query_time', 'query_mcat_name', 'call_duration', 'receiver_mobile'] as $column) {
                if (Schema::hasColumn('leads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
