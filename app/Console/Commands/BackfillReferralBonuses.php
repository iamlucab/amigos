<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\ReferralBonusLog;
use App\Models\ReferralConfiguration;
use Illuminate\Support\Facades\DB;

class BackfillReferralBonuses extends Command
{
    protected $signature = 'referrals:backfill';
    protected $description = 'Backfill referral bonuses for already approved members';

    public function handle()
    {
        $members = Member::where('status', 'Approved')
            ->whereNotNull('sponsor_id')
            ->get();

        $count = 0;
        
        // Get active configuration
        $config = ReferralConfiguration::getActive();
        
        if (!$config) {
            $this->error('No active referral configuration found');
            $this->info('Using default values (3 levels)');
        }

        foreach ($members as $member) {
            $alreadyLogged = ReferralBonusLog::where('referred_member_id', $member->id)->exists();
            if ($alreadyLogged) {
                continue;
            }

            DB::beginTransaction();
            try {
                if (!$config) {
                    // Fallback to default values if no configuration exists
                    $layer1 = $member->sponsor;
                    $layer2 = $layer1->sponsor ?? null;
                    $layer3 = $layer2->sponsor ?? null;

                    if ($layer1) {
                        ReferralBonusLog::create([
                            'member_id' => $layer1->id,
                            'referred_member_id' => $member->id,
                            'level' => 1,
                            'amount' => env('LEVEL_1_BONUS', 25),
                            'description' => "Direct referral bonus from {$member->full_name}"
                        ]);
                    }

                    if ($layer2) {
                        ReferralBonusLog::create([
                            'member_id' => $layer2->id,
                            'referred_member_id' => $member->id,
                            'level' => 2,
                            'amount' => env('LEVEL_2_BONUS', 15),
                            'description' => "2nd level referral bonus from {$member->full_name}"
                        ]);
                    }

                    if ($layer3) {
                        ReferralBonusLog::create([
                            'member_id' => $layer3->id,
                            'referred_member_id' => $member->id,
                            'level' => 3,
                            'amount' => env('LEVEL_3_BONUS', 10),
                            'description' => "3rd level referral bonus from {$member->full_name}"
                        ]);
                    }
                } else {
                    // Use dynamic configuration
                    $sponsor = $member->sponsor;
                    $level = 1;
                    
                    // Calculate all bonus amounts once
                    $bonuses = $config->getAllBonuses();
                    
                    while ($sponsor && $level <= $config->max_level) {
                        $bonusAmount = $bonuses[$level] ?? 0;
                        
                        if ($bonusAmount > 0) {
                            $levelText = $level == 1 ? "Direct" : "{$level}nd level";
                            
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

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error("Backfill failed for member ID {$member->id}: " . $e->getMessage());
            }
        }

        $this->info("✅ Referral bonuses backfilled for {$count} members.");
    }
}
