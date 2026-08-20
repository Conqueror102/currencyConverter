<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $code
 * @property string|null $numeric_code
 * @property string $name
 * @property string|null $symbol
 * @property int $minor_unit
 * @property Carbon|null $available_from
 * @property Carbon|null $available_until
 * @property bool $is_active
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'code',
    'numeric_code',
    'name',
    'symbol',
    'minor_unit',
    'available_from',
    'available_until',
    'is_active',
    'metadata',
])]
class Currency extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minor_unit' => 'integer',
            'is_active' => 'boolean',
            'available_from' => 'date',
            'available_until' => 'date',
            'metadata' => 'array',
        ];
    }

    /**
     * Scope query to only active currencies.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get rates where this currency is the base currency.
     */
    public function baseRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'base_currency', 'code');
    }

    /**
     * Get rates where this currency is the quote currency.
     */
    public function quoteRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'quote_currency', 'code');
    }
}
