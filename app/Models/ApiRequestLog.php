<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $api_key_id
 * @property string|null $ip_address
 * @property string $endpoint
 * @property string $method
 * @property int $status_code
 * @property int|null $response_time_ms
 * @property Carbon $created_at
 */
#[Fillable([
    'api_key_id',
    'ip_address',
    'endpoint',
    'method',
    'status_code',
    'response_time_ms',
])]
class ApiRequestLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'response_time_ms' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}
