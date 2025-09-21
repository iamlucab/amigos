<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Loan;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\RewardWinner;
use App\Models\ReferralBonusLog;
use App\Models\ReferralConfiguration;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthday',
        'mobile_number',
        'occupation',
        'photo',
        'address',
        'status',
        'role',
        'sponsor_id',
        'loan_eligible',
        'payment_method',
        'proof_of_payment',
    ];

    protected $casts = [
        'loan_eligible' => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    public function user()
    {
        return $this->hasOne(User::class, 'member_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(Member::class, 'sponsor_id');
    }

    public function sponsoredMembers()
    {
        return $this->hasMany(Member::class, 'sponsor_id');
    }

    public function wallet() // main wallet
    {
        return $this->hasOne(Wallet::class)->where('type', 'main');
    }

    public function cashbackWallet()
    {
        return $this->hasOne(Wallet::class)->where('type', 'cashback');
    }

    public function allWallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(WalletTransaction::class, Wallet::class);
    }

    public function rewardWins()
    {
        return $this->hasMany(RewardWinner::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function referralBonusLogs()
    {
        return $this->hasMany(ReferralBonusLog::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function membershipCode()
    {
        return $this->hasOneThrough(
            \App\Models\MembershipCode::class,
            \App\Models\User::class,
            'member_id', // Foreign key on users table
            'used_by',   // Foreign key on membership_codes table
            'id',        // Local key on members table
            'id'         // Local key on users table
        );
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute()
    {
        return now()->diffInYears($this->birthday);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : asset('images/default-profile.png');
    }

    public function getSponsorNameAttribute()
    {
        return $this->sponsor?->full_name ?? 'No Sponsor';
    }

    public function getRoleNameAttribute()
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'staff' => 'Staff',
            'member' => 'Member',
            default => 'Unknown Role',
        };
    }

    public function getWalletBalanceAttribute()
    {
        return $this->wallet?->balance ?? 0;
    }

    public function getCashbackBalanceAttribute()
    {
        return $this->cashbackWallet?->balance ?? 0;
    }

    public function getWalletTransactionsAttribute()
    {
        return $this->wallet?->transactions ?? collect();
    }

    public function getCashbackTransactionsAttribute()
    {
        return $this->cashbackWallet?->transactions ?? collect();
    }

    // ─── Booted: Wallet Creation + Bonuses ─────────────────────────

    protected static function booted()
    {
        static::created(function ($member) {
            DB::transaction(function () use ($member) {
                // Create main & cashback wallets
                $member->allWallets()->createMany([
                    [
                        'wallet_id' => Wallet::generateWalletId(),
                        'type' => 'main',
                        'balance' => 0,
                    ],
                    [
                        'wallet_id' => Wallet::generateWalletId(),
                        'type' => 'cashback',
                        'balance' => 0,
                    ],
                ]);

                // Note: Referral bonuses are now only applied when member status changes to 'Approved'
                // This is handled in MemberApprovalController and MembersController
            });
        });
    }

    // ─── Referral Methods ─────────────────────────────────────────────

    /**
     * Get all direct referrals (1st level)
     */
    public function getDirectReferrals()
    {
        return $this->sponsoredMembers()->where('status', 'Approved');
    }

    /**
     * Get referrals by specific level
     */
    public function getReferralsByLevel($level)
    {
        if ($level == 1) {
            return $this->getDirectReferrals();
        }

        $currentLevel = collect([$this]);
        
        for ($i = 1; $i < $level; $i++) {
            $nextLevel = collect();
            foreach ($currentLevel as $member) {
                $nextLevel = $nextLevel->merge($member->sponsoredMembers()->where('status', 'Approved')->get());
            }
            $currentLevel = $nextLevel;
        }
        
        return $currentLevel;
    }

    /**
     * Get count of referrals by level
     */
    public function getReferralCountByLevel($level)
    {
        return $this->getReferralsByLevel($level)->count();
    }

    /**
     * Get all referrals up to specified level with counts
     */
    public function getAllReferralCounts($maxLevel = 11)
    {
        $counts = [];
        for ($level = 1; $level <= $maxLevel; $level++) {
            $counts[$level] = $this->getReferralCountByLevel($level);
            // Stop if no referrals at this level
            if ($counts[$level] == 0) {
                break;
            }
        }
        return $counts;
    }

    /**
     * Get total referral count across all levels
     */
    public function getTotalReferralCount($maxLevel = 11)
    {
        return array_sum($this->getAllReferralCounts($maxLevel));
    }
}
