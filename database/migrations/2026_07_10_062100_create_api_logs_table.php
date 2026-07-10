<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50);
            $table->string('endpoint', 191)->nullable();
            $table->string('method', 10)->default('GET');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->json('request_headers')->nullable();
            $table->longText('request_body')->nullable();
            $table->longText('response_body')->nullable();
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->string('status', 30)->default('pending');
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('provider');
            $table->index('status');
            $table->index('lead_id');
            $table->index('created_at');
            $table->index(['provider', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
