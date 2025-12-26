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
        Schema::table('pricing_rules', function (Blueprint $table) {
            // morph untuk scope pricing (course / exam / dll)
            $table->nullableMorphs('priceable');

            // optional: index tambahan untuk performa
            $table->index(['product_type', 'is_active'], 'pricing_rules_product_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_rules', function (Blueprint $table) {
            $table->dropIndex('pricing_rules_product_active_idx');
            $table->dropMorphs('priceable');
        });
    }
};
