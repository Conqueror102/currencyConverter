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
        Schema::table('exchange_rates', function (Blueprint $table) {
            if (! Schema::hasColumn('exchange_rates', 'market_type')) {
                $table->string('market_type', 20)->default('official')->after('rate');
                $table->index(['base_currency', 'quote_currency', 'market_type', 'rate_date'], 'idx_rates_lookup_market');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            if (Schema::hasColumn('exchange_rates', 'market_type')) {
                $table->dropIndex('idx_rates_lookup_market');
                $table->dropColumn('market_type');
            }
        });
    }
};
