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
        Schema::create('exchange_rates', function (Blueprint $table): void {
            $table->id();
            $table->date('rate_date')->index();
            $table->string('base_currency', 3)->index();
            $table->string('quote_currency', 3)->index();
            $table->decimal('rate', 30, 15);
            $table->string('market_type', 20)->default('official')->index();
            $table->string('provider', 50)->index();
            $table->string('provider_reference', 100)->nullable();
            $table->timestamp('fetched_at')->useCurrent();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->unique(
                ['provider', 'rate_date', 'base_currency', 'quote_currency', 'market_type'],
                'unique_provider_rate_per_day'
            );

            $table->index(['base_currency', 'quote_currency', 'market_type', 'rate_date'], 'lookup_pair_by_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
