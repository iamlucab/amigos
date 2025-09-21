<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralBonusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'referred_member_id',
        'level',
        'amount',
        'description',
    ];

    public function member()
{
    return $this->belongsTo(Member::class, 'member_id');
}

public function referredMember()
{
    return $this->belongsTo(Member::class, 'referred_member_id');
}
}
