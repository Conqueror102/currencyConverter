<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $provider
 * @property string $sync_type
 * @property Carbon $started_at
 * @property Carbon|null $completed_at
 * @property string $status
 * @property int $records_received
 * @property int $records_saved
 * @property string|null $error_message
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'provider',
    'sync_type',
    'started_at',
    'completed_at',
    'status',
    'records_received',
    'records_saved',
    'error_message',
    'metadata',
])]
class RateSyncRun extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'records_received' => 'integer',
            'records_saved' => 'integer',
            'metadata' => 'array',
        ];
    }
}
