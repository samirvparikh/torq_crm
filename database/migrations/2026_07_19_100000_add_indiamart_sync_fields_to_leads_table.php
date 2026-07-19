<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Source table from the IndiaMART Message Centre bot (legacy spelling: inidamart_leads)
        if (! Schema::hasTable('inidamart_leads')) {
            Schema::create('inidamart_leads', function (Blueprint $table) {
                $table->id();
                $table->string('unique_query_id', 50)->unique();
                $table->string('query_type', 10)->nullable();
                $table->dateTime('query_time')->nullable();
                $table->string('sender_name', 200)->nullable();
                $table->string('sender_mobile', 30)->nullable();
                $table->string('sender_email', 150)->nullable();
                $table->string('sender_company', 200)->nullable();
                $table->text('sender_address')->nullable();
                $table->string('sender_city', 100)->nullable();
                $table->string('sender_state', 100)->nullable();
                $table->string('sender_pincode', 20)->nullable();
                $table->string('sender_country_iso', 10)->nullable();
                $table->string('sender_mobile_alt', 30)->nullable();
                $table->string('sender_email_alt', 150)->nullable();
                $table->string('query_product_name', 255)->nullable();
                $table->text('query_message')->nullable();
                $table->string('query_mcat_name', 255)->nullable();
                $table->string('call_duration', 50)->nullable();
                $table->string('receiver_mobile', 30)->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('query_time');
                $table->index('sender_mobile');
                $table->index('sender_city');
                $table->index('created_at');
            });
        }

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

        // Do not drop inidamart_leads — owned by the sync bot as well
    }
};
