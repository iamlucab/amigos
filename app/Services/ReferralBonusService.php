<?php

namespace App\Services;

use App\Models\Member;
use App\Models\ReferralBonusLog;
use App\Models\ReferralConfiguration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralBonusService
{
    /**
     * Award referral bonuses to sponsors when a member is approved
     */
    public static function awardReferralBonuses(Member $member): bool
    {
        // Only award bonuses if member has a sponsor and is approved
        if (!$member->sponsor || $member->status !== 'Approved') {
            return false;
        }

        DB::beginTransaction();
        try {
            // Get active configuration
            $config = ReferralConfiguration::getActive();
            
            if (!$config) {
                // Fallback to default values if no configuration exists
                self::awardDefaultBonuses($member);
            } else {
                // Use dynamic configuration
                self::awardConfiguredBonuses($member, $config);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Referral bonus failed for member ' . $member->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Award bonuses using default configuration
     */
    private static function awardDefaultBonuses(Member $member): void
    {
        $layer1 = $member->sponsor;
        $layer2 = $layer1->sponsor ?? null;
        $layer3 = $layer2->sponsor ?? null;

        if ($layer1 && $layer1->cashbackWallet) {
            $amount = env('LEVEL_1_BONUS', 25);
            $layer1->cashbackWallet->credit($amount, "Direct referral bonus from {$member->full_name}");
            ReferralBonusLog::create([
                'member_id'          => $layer1->id,
                'referred_member_id' => $member->id,
                'level'              => 1,
                'amount'             => $amount,
                'description'        => "Direct referral bonus from {$member->full_name}"
            ]);
        }

        if ($layer2 && $layer2->cashbackWallet) {
            $amount = env('LEVEL_2_BONUS', 15);
            $layer2->cashbackWallet->credit($amount, "2nd level referral bonus from {$member->full_name}");
            ReferralBonusLog::create([
                'member_id'          => $layer2->id,
                'referred_member_id' => $member->id,
                'level'              => 2,
                'amount'             => $amount,
                'description'        => "2nd level referral bonus from {$member->full_name}"
            ]);
        }

        if ($layer3 && $layer3->cashbackWallet) {
            $amount = env('LEVEL_3_BONUS', 10);
            $layer3->cashbackWallet->credit($amount, "3rd level referral bonus from {$member->full_name}");
            ReferralBonusLog::create([
                'member_id'          => $layer3->id,
                'referred_member_id' => $member->id,
                'level'              => 3,
                'amount'             => $amount,
                'description'        => "3rd level referral bonus from {$member->full_name}"
            ]);
        }
    }

    /**
     * Award bonuses using configured settings
     */
    private static function awardConfiguredBonuses(Member $member, ReferralConfiguration $config): void
    {
        $sponsor = $member->sponsor;
        $level = 1;
        
        // Calculate all bonus amounts once
        $bonuses = $config->getAllBonuses();
        
        while ($sponsor && $level <= $config->max_level) {
            $bonusAmount = $bonuses[$level] ?? 0;
            
            if ($bonusAmount > 0 && $sponsor->cashbackWallet) {
                $levelText = $level == 1 ? "Direct" : "{$level}nd level";
                $sponsor->cashbackWallet->credit(
                    $bonusAmount,
                    "{$levelText} referral bonus from {$member->full_name}"
                );
                
                ReferralBonusLog::create([
                    'member_id' => $sponsor->id,
                    'referred_member_id' => $member->id,
                    'level' => $level,
                    'amount' => $bonusAmount,
                    'description' => "{$levelText} referral bonus from {$member->full_name}"
                ]);
            }
            
            $sponsor = $sponsor->sponsor;
            $level++;
        }
    }

    /**
     * Check if referral bonuses have already been awarded for this member
     */
    public static function bonusesAlreadyAwarded(Member $member): bool
    {
        return ReferralBonusLog::where('referred_member_id', $member->id)->exists();
    }
}