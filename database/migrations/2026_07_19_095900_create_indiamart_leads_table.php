<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('indiamart_leads')) {
            return;
        }

        Schema::create('indiamart_leads', function (Blueprint $table) {
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

    public function down(): void
    {
        // Do not drop — table may also be owned by the IndiaMART sync bot
    }
};
