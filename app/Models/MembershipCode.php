<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MembershipCode extends Model
{
    protected $fillable = ['code', 'used', 'used_by', 'used_at', 'reserved', 'reserved_by', 'reserved_at'];
    
    protected $casts = [
        'used_at' => 'datetime',
        'reserved_at' => 'datetime',
    ];

    public static function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return self::create(['code' => $code]);
    }

    public function markAsUsed($userId)
    {
        $this->update([
            'used' => true,
            'used_by' => $userId,
            'used_at' => now(),
            'reserved' => false,
            'reserved_by' => null,
            'reserved_at' => null
        ]);
    }

    public function markAsReserved($userId)
    {
        $this->update([
            'reserved' => true,
            'reserved_by' => $userId,
            'reserved_at' => now(),
            'used' => false,
            'used_by' => null,
            'used_at' => null
        ]);
    }

    public function releaseReservation()
    {
        $this->update([
            'reserved' => false,
            'reserved_by' => null,
            'reserved_at' => null
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function reservedBy()
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }
    
    public function requestItems()
    {
        return $this->hasMany(MembershipCodeRequestItem::class);
    }
    
    public function membershipCodeRequests()
    {
        return $this->belongsToMany(MembershipCodeRequest::class, 'membership_code_request_items', 'membership_code_id', 'membership_code_request_id');
    }

    public function scopeUnused($query)
    {
        return $query->where('used', false);
    }

    public function scopeUnreserved($query)
    {
        return $query->where('reserved', false);
    }

    public function scopeAvailable($query)
    {
        return $query->where('used', false)->where('reserved', false);
    }

    public function getStatusAttribute()
    {
        if ($this->used) {
            return 'used';
        } elseif ($this->reserved) {
            return 'reserved';
        } else {
            return 'available';
        }
    }
}
