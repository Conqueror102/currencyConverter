<?php

namespace App\Console\Commands;

use App\Domain\ExchangeRates\Services\RateSyncService;
use Illuminate\Console\Command;

class SyncParallelRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:sync-parallel {--provider= : Custom provider name to use}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Synchronize live crypto P2P and street parallel market exchange rates';

    /**
     * Execute the console command.
     */
    public function handle(RateSyncService $syncService): int
    {
        $provider = $this->option('provider') ?: 'parallel_p2p';
        $this->info("Fetching real-time Parallel / P2P exchange rates from [{$provider}]...");

        $result = $syncService->syncParallelRates($provider);

        if ($result['status'] === 'success') {
            $this->info("Successfully saved {$result['saved']} parallel market rates (Received: {$result['received']}).");

            return self::SUCCESS;
        }

        $this->error('Failed to synchronize parallel market exchange rates.');

        return self::FAILURE;
    }
}
