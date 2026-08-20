<?php

namespace App\Console\Commands;

use App\Domain\ExchangeRates\Services\RateSyncService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class BackfillRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:backfill 
                            {--from= : Start date (YYYY-MM-DD)}
                            {--to= : End date (YYYY-MM-DD)}
                            {--provider= : Specific provider to sync from}
                            {--base=EUR : Base currency to request from provider}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill historical exchange rates within a date range';

    public function handle(RateSyncService $syncService): int
    {
        $fromStr = $this->option('from') ?? CarbonImmutable::today()->subMonths(6)->toDateString();
        $toStr = $this->option('to') ?? CarbonImmutable::today()->toDateString();
        $provider = $this->option('provider');
        $base = (string) $this->option('base');

        $from = CarbonImmutable::parse($fromStr);
        $to = CarbonImmutable::parse($toStr);

        $this->info("Starting rate backfill from {$from->toDateString()} to {$to->toDateString()}...");

        $result = $syncService->backfillRates($from, $to, $provider, $base);

        if ($result['status'] === 'success') {
            $this->info("Backfill complete. Saved {$result['saved']} records.");

            return Command::SUCCESS;
        }

        $this->error('Backfill failed. Check logs for details.');

        return Command::FAILURE;
    }
}
