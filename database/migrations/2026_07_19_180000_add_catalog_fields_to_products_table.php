<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('capacity', 191)->nullable()->after('description');
            $table->text('operation')->nullable()->after('capacity');
            $table->json('technical_specifications')->nullable()->after('operation');
            $table->json('input_specifications')->nullable()->after('technical_specifications');
            $table->json('salient_features')->nullable()->after('input_specifications');
            $table->json('utility_requirements')->nullable()->after('salient_features');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'capacity',
                'operation',
                'technical_specifications',
                'input_specifications',
                'salient_features',
                'utility_requirements',
            ]);
        });
    }
};
