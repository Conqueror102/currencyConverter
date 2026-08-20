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
        Schema::create('rate_sync_runs', function (Blueprint $table): void {
            $table->id();
            $table->string('provider', 50)->index();
            $table->string('sync_type', 50)->index(); // 'currencies', 'latest_rates', 'backfill'
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 20)->default('running')->index(); // 'running', 'success', 'failed'
            $table->unsignedInteger('records_received')->default(0);
            $table->unsignedInteger('records_saved')->default(0);
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_sync_runs');
    }
};
