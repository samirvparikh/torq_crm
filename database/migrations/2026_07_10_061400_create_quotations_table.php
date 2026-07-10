<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 50)->unique();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();

            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->string('status', 30)->default('Draft');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('discount_type', 20)->nullable();
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->string('pdf_path', 191)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('lead_id');
            $table->index('customer_id');
            $table->index('company_id');
            $table->index('status');
            $table->index('quotation_date');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
