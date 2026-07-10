<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_number', 50)->unique();
            $table->foreignId('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('indiamart_lead_id', 100)->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('customer_name', 191);
            $table->string('company_name', 191)->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('alternate_mobile', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('website', 191)->nullable();

            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('India');
            $table->string('pincode', 10)->nullable();

            $table->string('interested_product', 191)->nullable();
            $table->text('requirement')->nullable();
            $table->string('quantity', 100)->nullable();
            $table->decimal('budget', 15, 2)->nullable();

            $table->string('priority', 20)->default('Medium');
            $table->string('status', 30)->default('New');
            $table->string('lost_reason', 191)->nullable();

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('expected_closing_date')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_followup_at')->nullable();
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->decimal('won_value', 15, 2)->nullable();

            $table->json('raw_data')->nullable();
            $table->boolean('is_duplicate')->default(false);
            $table->foreignId('duplicate_of_lead_id')->nullable()->constrained('leads')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('indiamart_lead_id');
            $table->index('lead_source_id');
            $table->index('status');
            $table->index('priority');
            $table->index('assigned_to');
            $table->index('created_by');
            $table->index('customer_id');
            $table->index('company_id');
            $table->index('mobile');
            $table->index('email');
            $table->index('company_name');
            $table->index(['city', 'state']);
            $table->index('expected_closing_date');
            $table->index('next_followup_at');
            $table->index('created_at');
            $table->index(['status', 'assigned_to']);
            $table->index(['lead_source_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
