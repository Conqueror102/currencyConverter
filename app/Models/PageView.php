<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $session_id
 * @property string|null $ip_address
 * @property string $path
 * @property string|null $referrer
 * @property string $device_type
 * @property string $browser
 * @property string $country_code
 * @property Carbon $created_at
 */
#[Fillable([
    'session_id',
    'ip_address',
    'path',
    'referrer',
    'device_type',
    'browser',
    'country_code',
])]
class PageView extends Model
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
        ];
    }
}
