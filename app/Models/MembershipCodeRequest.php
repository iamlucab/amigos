<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipCodeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'quantity',
        'amount_per_code',
        'total_amount',
        'payment_method',
        'status',
        'proof_path',
        'note',
        'reserved_codes_count'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Payment method constants
    const PAYMENT_METHOD_GCASH = 'GCash';
    const PAYMENT_METHOD_BANK = 'Bank';
    const PAYMENT_METHOD_WALLET = 'Wallet';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
    }

    public static function getPaymentMethodOptions()
    {
        return [
            self::PAYMENT_METHOD_GCASH => 'GCash',
            self::PAYMENT_METHOD_BANK => 'Bank Transfer',
            self::PAYMENT_METHOD_WALLET => 'Wallet'
        ];
    }

    public function getProofUrlAttribute()
    {
        return $this->proof_path ? asset('storage/' . $this->proof_path) : null;
    }
    
    public function requestItems()
    {
        return $this->hasMany(MembershipCodeRequestItem::class);
    }
    
    public function reservedCodes()
    {
        return $this->belongsToMany(MembershipCode::class, 'membership_code_request_items', 'membership_code_request_id', 'membership_code_id');
    }
}
