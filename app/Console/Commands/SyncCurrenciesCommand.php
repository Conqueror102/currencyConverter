<?php

namespace App\Console\Commands;

use App\Domain\ExchangeRates\Services\RateSyncService;
use Illuminate\Console\Command;

class SyncCurrenciesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:sync-currencies {--provider= : Specific provider to sync from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active currencies and metadata from the exchange rate provider';

    public function handle(RateSyncService $syncService): int
    {
        $provider = $this->option('provider');
        $this->info('Starting currency synchronization...');

        $result = $syncService->syncCurrencies($provider);

        if ($result['status'] === 'success') {
            $this->info("Successfully synchronized {$result['saved']} currencies (Received: {$result['received']}).");

            return Command::SUCCESS;
        }

        $this->error('Currency synchronization failed. Check application logs for details.');

        return Command::FAILURE;
    }
}
