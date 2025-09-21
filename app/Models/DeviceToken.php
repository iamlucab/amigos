<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_token',
        'device_type',
        'platform',
        'device_id',
        'app_version',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the device token
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get tokens by platform
     */
    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get tokens by device type
     */
    public function scopeDeviceType($query, $type)
    {
        return $query->where('device_type', $type);
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed()
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Deactivate the token
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate the token
     */
    public function activate()
    {
        $this->update(['is_active' => true, 'last_used_at' => now()]);
    }

    /**
     * Check if token is expired (not used for 30 days)
     */
    public function isExpired()
    {
        if (!$this->last_used_at) {
            return false;
        }
        
        return $this->last_used_at->lt(Carbon::now()->subDays(30));
    }

    /**
     * Get platform icon
     */
    public function getPlatformIconAttribute()
    {
        return match ($this->platform) {
            'ios' => 'fab fa-apple',
            'android' => 'fab fa-android',
            'web' => 'fas fa-globe',
            default => 'fas fa-mobile-alt',
        };
    }

    /**
     * Get platform name
     */
    public function getPlatformNameAttribute()
    {
        return match ($this->platform) {
            'ios' => 'iOS',
            'android' => 'Android',
            'web' => 'Web Browser',
            default => 'Mobile Device',
        };
    }

    /**
     * Create or update device token
     */
    public static function createOrUpdate($userId, $tokenData)
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'device_token' => $tokenData['device_token'],
            ],
            [
                'device_type' => $tokenData['device_type'] ?? 'mobile',
                'platform' => $tokenData['platform'] ?? null,
                'device_id' => $tokenData['device_id'] ?? null,
                'app_version' => $tokenData['app_version'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Clean up expired tokens
     */
    public static function cleanupExpired()
    {
        return static::where('last_used_at', '<', Carbon::now()->subDays(30))
            ->delete();
    }

    /**
     * Get active tokens for user
     */
    public static function getActiveTokensForUser($userId, $platform = null)
    {
        $query = static::where('user_id', $userId)->active();
        
        if ($platform) {
            $query->platform($platform);
        }
        
        return $query->get();
    }
}
