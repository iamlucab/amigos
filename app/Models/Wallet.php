<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'balance',
        'wallet_id',
        'type', // 'main' or 'cashback'
        'user_id',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateWalletId()
    {
        return 'WALLET-' . strtoupper(uniqid());
    }

    /**
     * Add funds to the wallet and log the transaction
     */
    public function credit($amount, $description = 'Wallet Credit', $relatedMemberId = null)
    {
        $this->increment('balance', $amount);

        $this->transactions()->create([
            'type'              => 'credit',
            'amount'            => $amount,
            'description'       => $description,
            'related_member_id' => $relatedMemberId,
            'member_id'         => $this->member_id,
        ]);
    }

    /**
     * Deduct funds from the wallet if sufficient balance, and log the transaction
     */
    public function debit($amount, $description = 'Wallet Debit', $relatedMemberId = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $this->decrement('balance', $amount);

        $this->transactions()->create([
            'type'              => 'debit',
            'amount'            => $amount,
            'description'       => $description,
            'related_member_id' => $relatedMemberId,
            'member_id'         => $this->member_id,
        ]);
    }

    /**
     * Accessor fallback for balance
     */
    public function getBalanceAttribute()
    {
        return $this->attributes['balance'] ?? 0;
    }
}
