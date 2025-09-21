<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
protected $fillable = [
    'name',
    'email',
    'password',
    'mobile_number',
    'role',
    'member_id',
    'status',
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getAuthIdentifierName()
    {
        return 'mobile_number';
    }
    
    /**
     * Get the name of the unique identifier for the user for email auth
     */
    public function getEmailAuthIdentifierName()
    {
        return 'email';
    }
    
    /**
     * Find user by email or mobile number
     */
    public static function findForAuth($login)
    {
        // Check if login is email format
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return static::where('email', $login)->first();
        }
        
        // Otherwise treat as mobile number
        return static::where('mobile_number', $login)->first();
    }

public function member()
{
    return $this->belongsTo(Member::class);
}



public function wallet()
{
    return $this->hasOne(\App\Models\Wallet::class);
}

// Role helper methods
public function isAdmin()
{
    return $this->role === 'Admin';
}

public function isStaff()
{
    return $this->role === 'Staff';
}

public function isMember()
{
    return $this->role === 'Member';
}

// Products created by this user (for staff)
public function createdProducts()
{
    return $this->hasMany(\App\Models\Product::class, 'created_by');
}

// Device tokens for push notifications
public function deviceTokens()
{
    return $this->hasMany(\App\Models\DeviceToken::class);
}

// Get active device tokens
public function activeDeviceTokens()
{
    return $this->hasMany(\App\Models\DeviceToken::class)->active();
}

}
