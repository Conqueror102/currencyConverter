<?php

namespace Database\Seeders;

use App\Models\ConversionLog;
use App\Models\PageView;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AnalyticsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't re-seed if there are already records
        if (PageView::count() > 50) {
            return;
        }

        $now = Carbon::now();
        $paths = ['/', '/', '/', '/docs', '/docs', '/login', '/dashboard'];
        $browsers = ['Chrome', 'Chrome', 'Chrome', 'Safari', 'Safari', 'Firefox', 'Edge'];
        $devices = ['desktop', 'desktop', 'mobile', 'mobile', 'tablet'];
        $countries = ['NG', 'NG', 'US', 'US', 'GB', 'DE', 'GH', 'KE', 'AR'];

        $pageViews = [];

        // Generate 14 days of realistic page views
        for ($day = 13; $day >= 0; $day--) {
            $date = $now->copy()->subDays($day);
            $visitsCount = rand(45, 120);

            for ($i = 0; $i < $visitsCount; $i++) {
                $createdAt = $date->copy()->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
                $pageViews[] = [
                    'session_id' => bin2hex(random_bytes(16)),
                    'ip_address' => '197.'.rand(10, 250).'.'.rand(1, 254).'.'.rand(1, 254),
                    'path' => $paths[array_rand($paths)],
                    'referrer' => rand(0, 1) ? 'https://google.com' : null,
                    'device_type' => $devices[array_rand($devices)],
                    'browser' => $browsers[array_rand($browsers)],
                    'country_code' => $countries[array_rand($countries)],
                    'created_at' => $createdAt,
                ];
            }
        }

        foreach (array_chunk($pageViews, 200) as $chunk) {
            PageView::insert($chunk);
        }

        // Seed Conversion Logs
        $pairs = [
            ['from' => 'USD', 'to' => 'NGN', 'market' => 'parallel', 'rate' => 1404.00, 'weight' => 45],
            ['from' => 'USD', 'to' => 'NGN', 'market' => 'official', 'rate' => 1347.74, 'weight' => 20],
            ['from' => 'EUR', 'to' => 'USD', 'market' => 'official', 'rate' => 1.0850, 'weight' => 15],
            ['from' => 'GBP', 'to' => 'NGN', 'market' => 'parallel', 'rate' => 1840.00, 'weight' => 10],
            ['from' => 'USD', 'to' => 'ARS', 'market' => 'parallel', 'rate' => 1280.00, 'weight' => 8],
            ['from' => 'USD', 'to' => 'GHS', 'market' => 'parallel', 'rate' => 15.60, 'weight' => 7],
            ['from' => 'USD', 'to' => 'JPY', 'market' => 'official', 'rate' => 155.20, 'weight' => 5],
        ];

        $actions = ['mid', 'buy', 'sell'];
        $sources = ['web_ui', 'web_ui', 'api'];
        $conversionLogs = [];

        for ($day = 13; $day >= 0; $day--) {
            $date = $now->copy()->subDays($day);
            $conversionsCount = rand(25, 75);

            for ($i = 0; $i < $conversionsCount; $i++) {
                $pair = $pairs[array_rand($pairs)];
                $amt = rand(10, 500) * 10;
                $rate = $pair['rate'] * (1 + (rand(-10, 10) / 1000));
                $result = round($amt * $rate, 2);
                $createdAt = $date->copy()->setTime(rand(0, 23), rand(0, 59), rand(0, 59));

                $conversionLogs[] = [
                    'source' => $sources[array_rand($sources)],
                    'api_key_id' => null,
                    'base_currency' => $pair['from'],
                    'quote_currency' => $pair['to'],
                    'market_type' => $pair['market'],
                    'trade_action' => $actions[array_rand($actions)],
                    'amount' => $amt,
                    'rate' => $rate,
                    'result' => $result,
                    'ip_address' => '102.'.rand(10, 250).'.'.rand(1, 254).'.'.rand(1, 254),
                    'created_at' => $createdAt,
                ];
            }
        }

        foreach (array_chunk($conversionLogs, 200) as $chunk) {
            ConversionLog::insert($chunk);
        }
    }
}
