<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name', 191);
            $table->string('quantity', 100)->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('lead_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_products');
    }
};
