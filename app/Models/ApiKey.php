<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string $key
 * @property string $secret_prefix
 * @property string $tier
 * @property int $rate_limit_per_minute
 * @property bool $is_active
 * @property Carbon|null $last_used_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'user_id',
    'name',
    'key',
    'secret_prefix',
    'tier',
    'rate_limit_per_minute',
    'is_active',
    'last_used_at',
    'expires_at',
])]
#[Hidden(['key'])]
class ApiKey extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'rate_limit_per_minute' => 'integer',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requestLogs(): HasMany
    {
        return $this->hasMany(ApiRequestLog::class);
    }

    /**
     * Generate a new secure API Key with prefix.
     *
     * @return array{apiKey: self, plainToken: string}
     */
    public static function createToken(
        string $name,
        ?User $user = null,
        string $tier = 'free',
        int $rateLimit = 60
    ): array {
        $prefix = 'cc_live_'.Str::random(6);
        $secret = Str::random(32);
        $plainToken = "{$prefix}_{$secret}";

        $apiKey = self::create([
            'user_id' => $user?->id,
            'name' => $name,
            'secret_prefix' => $prefix,
            'key' => hash('sha256', $plainToken),
            'tier' => $tier,
            'rate_limit_per_minute' => $rateLimit,
            'is_active' => true,
        ]);

        return [
            'apiKey' => $apiKey,
            'plainToken' => $plainToken,
        ];
    }

    /**
     * Find an active API key by plain token string.
     */
    public static function findByToken(string $token): ?self
    {
        $hash = hash('sha256', trim($token));

        return self::where('key', $hash)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
    }
}
