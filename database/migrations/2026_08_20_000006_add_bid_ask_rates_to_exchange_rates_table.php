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
            if (! Schema::hasColumn('exchange_rates', 'buy_rate')) {
                $table->decimal('buy_rate', 30, 15)->nullable()->after('rate');
            }
            if (! Schema::hasColumn('exchange_rates', 'sell_rate')) {
                $table->decimal('sell_rate', 30, 15)->nullable()->after('buy_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            if (Schema::hasColumn('exchange_rates', 'buy_rate')) {
                $table->dropColumn('buy_rate');
            }
            if (Schema::hasColumn('exchange_rates', 'sell_rate')) {
                $table->dropColumn('sell_rate');
            }
        });
    }
};
