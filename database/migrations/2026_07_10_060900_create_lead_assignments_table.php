<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at');
            $table->text('notes')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            $table->index(['lead_id', 'is_current']);
            $table->index('assigned_to');
            $table->index('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_assignments');
    }
};
