<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $rate_date
 * @property string $base_currency
 * @property string $quote_currency
 * @property string $rate
 * @property string|null $buy_rate
 * @property string|null $sell_rate
 * @property string $market_type
 * @property string $provider
 * @property string|null $provider_reference
 * @property Carbon $fetched_at
 * @property array<string, mixed>|null $raw_payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'rate_date',
    'base_currency',
    'quote_currency',
    'rate',
    'buy_rate',
    'sell_rate',
    'market_type',
    'provider',
    'provider_reference',
    'fetched_at',
    'raw_payload',
])]
class ExchangeRate extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rate_date' => 'date',
            'fetched_at' => 'datetime',
            'raw_payload' => 'array',
            'rate' => 'string',
            'buy_rate' => 'string',
            'sell_rate' => 'string',
            'market_type' => 'string',
        ];
    }

    public function base(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'base_currency', 'code');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'quote_currency', 'code');
    }

    /**
     * Scope for official central bank rates.
     */
    public function scopeOfficial(Builder $query): Builder
    {
        return $query->where('market_type', 'official');
    }

    /**
     * Scope for parallel/street market rates.
     */
    public function scopeParallel(Builder $query): Builder
    {
        return $query->where('market_type', 'parallel');
    }

    /**
     * Scope for a specific currency pair.
     */
    public function scopePair(Builder $query, string $base, string $quote): Builder
    {
        return $query->where('base_currency', $base)->where('quote_currency', $quote);
    }
}
