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
        Schema::create('page_views', function (Blueprint $table): void {
            $table->id();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('path')->index();
            $table->string('referrer')->nullable();
            $table->string('device_type', 20)->default('desktop')->index(); // desktop, mobile, tablet
            $table->string('browser', 30)->default('Chrome')->index();
            $table->string('country_code', 5)->default('US')->index();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('conversion_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 20)->default('web_ui')->index(); // web_ui, api
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->string('base_currency', 3)->index();
            $table->string('quote_currency', 3)->index();
            $table->string('market_type', 15)->default('official')->index(); // official, parallel
            $table->string('trade_action', 10)->default('mid')->index(); // mid, buy, sell
            $table->decimal('amount', 20, 4);
            $table->decimal('rate', 20, 6);
            $table->decimal('result', 20, 4);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversion_logs');
        Schema::dropIfExists('page_views');
    }
};
