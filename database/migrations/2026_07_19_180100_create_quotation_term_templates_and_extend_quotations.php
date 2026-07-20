<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_term_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('slug', 100)->unique();
            $table->text('content');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->string('subject', 255)->nullable()->after('company_id');
            $table->text('intro_text')->nullable()->after('subject');
            $table->string('signatory_name', 191)->nullable()->after('intro_text');
            $table->string('signatory_phone', 30)->nullable()->after('signatory_name');
            $table->string('tax_type', 20)->default('igst')->after('discount_amount');
            $table->foreignId('quotation_term_template_id')->nullable()->after('terms')
                ->constrained('quotation_term_templates')->nullOnDelete();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->string('capacity', 191)->nullable()->after('description');
            $table->text('operation')->nullable()->after('capacity');
            $table->json('technical_specifications')->nullable()->after('operation');
            $table->json('input_specifications')->nullable()->after('technical_specifications');
            $table->json('salient_features')->nullable()->after('input_specifications');
            $table->json('utility_requirements')->nullable()->after('salient_features');
            $table->boolean('include_catalog')->default(true)->after('utility_requirements');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn([
                'capacity',
                'operation',
                'technical_specifications',
                'input_specifications',
                'salient_features',
                'utility_requirements',
                'include_catalog',
            ]);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quotation_term_template_id');
            $table->dropColumn([
                'subject',
                'intro_text',
                'signatory_name',
                'signatory_phone',
                'tax_type',
            ]);
        });

        Schema::dropIfExists('quotation_term_templates');
    }
};
