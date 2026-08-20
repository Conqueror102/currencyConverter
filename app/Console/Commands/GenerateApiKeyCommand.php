<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateApiKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-keys:generate 
                            {name : Name or identifier for the API key}
                            {--user= : User ID or email associated with this key}
                            {--tier=free : Subscription tier (free, developer, enterprise)}
                            {--rate-limit=60 : Rate limit per minute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new secure API Key for public API access';

    public function handle(): int
    {
        $name = (string) $this->argument('name');
        $userIdentifier = $this->option('user');
        $tier = (string) $this->option('tier');
        $rateLimit = (int) $this->option('rate-limit');

        $user = null;
        if ($userIdentifier) {
            $user = is_numeric($userIdentifier)
                ? User::find($userIdentifier)
                : User::where('email', $userIdentifier)->first();
        }

        $result = ApiKey::createToken(
            name: $name,
            user: $user,
            tier: $tier,
            rateLimit: $rateLimit
        );

        $this->newLine();
        $this->info('🎉 New API Key Generated Successfully!');
        $this->table(
            ['Field', 'Value'],
            [
                ['Key Name', $result['apiKey']->name],
                ['Key ID', (string) $result['apiKey']->id],
                ['Prefix', $result['apiKey']->secret_prefix],
                ['Tier', $result['apiKey']->tier],
                ['Rate Limit', "{$result['apiKey']->rate_limit_per_minute} req/min"],
                ['API Token (Secret)', $result['plainToken']],
            ]
        );
        $this->warn('⚠️  Make sure to copy the API Token now. You will not be able to see it again!');
        $this->newLine();

        return Command::SUCCESS;
    }
}
