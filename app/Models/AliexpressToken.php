<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AliexpressToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'token_type',
        'expires_in',
        'expires_at',
        'scope',
        'aliexpress_user_id',
        'aliexpress_login_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'expires_in' => 'integer',
    ];

    /**
     * Get the user that owns the token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the token has expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the token is expiring soon (within 5 minutes).
     *
     * @return bool
     */
    public function isExpiringSoon()
    {
        return $this->expires_at && $this->expires_at->diffInMinutes(now()) <= 5;
    }
}
