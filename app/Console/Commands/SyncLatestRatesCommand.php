<?php

namespace App\Console\Commands;

use App\Domain\ExchangeRates\Services\RateSyncService;
use Illuminate\Console\Command;

class SyncLatestRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:sync-latest 
                            {--provider= : Specific provider to sync from}
                            {--base=EUR : Base currency to request from provider}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync latest exchange rates from the active rate provider';

    public function handle(RateSyncService $syncService): int
    {
        $provider = $this->option('provider');
        $base = (string) $this->option('base');

        $this->info("Fetching latest exchange rates (Base: {$base})...");

        $result = $syncService->syncLatestRates($provider, $base);

        if ($result['status'] === 'success') {
            $this->info("Successfully saved {$result['saved']} rates (Received: {$result['received']}).");

            return Command::SUCCESS;
        }

        $this->error('Failed to synchronize latest rates. Check logs for details.');

        return Command::FAILURE;
    }
}
