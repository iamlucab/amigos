<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Wallet;
use App\Models\Member;

class WalletTransaction extends Model
{
    use HasFactory;

    
protected $fillable = [
    'wallet_id', 'member_id', 'amount', 'type',
    'description', 'notes', 'source',
];
    
    // ─── Relationships ─────────────────────────────────────────────

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function member()
    {
        return $this->wallet?->member;
    }

    public function relatedMember()
    {
        return $this->belongsTo(Member::class, 'related_member_id');
    }

    // ─── Static Helpers ────────────────────────────────────────────

    /**
     * Credit a wallet and log the transaction.
     */
    public static function creditTo(Wallet $wallet, float $amount, string $description = null, $relatedMemberId = null, string $source = null)
    {
        $wallet->increment('balance', $amount);

        return self::create([
            'wallet_id'         => $wallet->id,
            'member_id'         => $wallet->member_id,
            'type'              => 'credit',
            'amount'            => $amount,
            'description'       => $description ?? 'Credit',
            'related_member_id' => $relatedMemberId,
            'source'            => $source,
        ]);
    }

    /**
     * Debit a wallet and log the transaction.
     * Will throw an exception if balance is insufficient.
     */
    public static function debitFrom(Wallet $wallet, float $amount, string $description = null, $relatedMemberId = null, string $source = null)
    {
        if ($wallet->balance < $amount) {
            throw new \Exception("Insufficient balance in wallet.");
        }

        $wallet->decrement('balance', $amount);

        return self::create([
            'wallet_id'         => $wallet->id,
            'member_id'         => $wallet->member_id,
            'type'              => 'debit',
            'amount'            => $amount, // Positive value stored
            'description'       => $description ?? 'Debit',
            'related_member_id' => $relatedMemberId,
            'source'            => $source,
        ]);
    }
}
