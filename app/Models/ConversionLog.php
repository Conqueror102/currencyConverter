<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $source
 * @property int|null $api_key_id
 * @property string $base_currency
 * @property string $quote_currency
 * @property string $market_type
 * @property string $trade_action
 * @property string $amount
 * @property string $rate
 * @property string $result
 * @property string|null $ip_address
 * @property Carbon $created_at
 */
#[Fillable([
    'source',
    'api_key_id',
    'base_currency',
    'quote_currency',
    'market_type',
    'trade_action',
    'amount',
    'rate',
    'result',
    'ip_address',
])]
class ConversionLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'amount' => 'decimal:4',
            'rate' => 'decimal:6',
            'result' => 'decimal:4',
        ];
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}
