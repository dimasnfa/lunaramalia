<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rekomendasi_menu', function (Blueprint $table) {
            // Tambah kolom untuk algoritma Apriori
            $table->decimal('confidence', 5, 2)->nullable()->after('recommended_menu_ids')->comment('Confidence score (0.00-100.00)');
            $table->decimal('support', 5, 3)->nullable()->after('confidence')->comment('Support score (0.000-1.000)');
            $table->decimal('lift', 5, 2)->nullable()->after('support')->comment('Lift score');
            $table->integer('frequency_count')->nullable()->after('lift')->comment('Frequency count');
            $table->timestamp('last_calculated_at')->nullable()->after('frequency_count')->comment('Last calculation time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekomendasi_menu', function (Blueprint $table) {
            $table->dropColumn([
                'confidence',
                'support', 
                'lift',
                'frequency_count',
                'last_calculated_at'
            ]);
        });
    }
};