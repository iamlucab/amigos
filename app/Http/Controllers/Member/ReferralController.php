<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReferralController extends Controller
{
    /**
     * Display referral summary for the authenticated member
     */
    public function summary(Request $request)
    {
        $member = auth()->user()->member;
        
        // Get date range from request or default to current month
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Get selected level filter (default to all levels)
        $selectedLevel = $request->get('level', 'all');
        
        // Get all referral levels data
        $referralData = [];
        $totalReferrals = 0;
        $totalBonusEarned = 0;
        
        for ($level = 1; $level <= 11; $level++) {
            $referrals = $member->getReferralsByLevel($level);
            
            // Filter by date range if provided
            if ($startDate && $endDate) {
                if ($referrals instanceof \Illuminate\Database\Eloquent\Builder) {
                    // For query builders (level 1)
                    $referrals = $referrals->whereBetween('created_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
                } else {
                    // For collections (levels > 1)
                    $referrals = $referrals->whereBetween('created_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
                }
            }
            
            $referralCount = $referrals->count();
            
            // Calculate bonus earned from this level
            $bonusEarned = $member->referralBonusLogs()
                ->where('level', $level)
                ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
                })
                ->sum('amount');
            
            if ($referralCount > 0 || $bonusEarned > 0) {
                $referralData[$level] = [
                    'level' => $level,
                    'count' => $referralCount,
                    'bonus_earned' => $bonusEarned,
                    'referrals' => $referrals instanceof \Illuminate\Database\Eloquent\Builder ? $referrals->get() : $referrals
                ];
                
                $totalReferrals += $referralCount;
                $totalBonusEarned += $bonusEarned;
            }
        }
        
        // Filter by specific level if requested
        if ($selectedLevel !== 'all' && is_numeric($selectedLevel)) {
            $referralData = array_filter($referralData, function($data) use ($selectedLevel) {
                return $data['level'] == $selectedLevel;
            });
        }
        
        // Get recent referral bonus logs for activity feed
        $recentBonusLogs = $member->referralBonusLogs()
            ->with('referredMember')
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            })
            ->latest()
            ->take(10)
            ->get();
        
        return view('members.referral-summary', compact(
            'member',
            'referralData',
            'totalReferrals',
            'totalBonusEarned',
            'recentBonusLogs',
            'startDate',
            'endDate',
            'selectedLevel'
        ));
    }
    
    /**
     * Get referral details for a specific level (AJAX)
     */
    public function levelDetails(Request $request, $level)
    {
        $member = auth()->user()->member;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $referrals = $member->getReferralsByLevel($level);
        
        // Filter by date range if provided
        if ($startDate && $endDate) {
            if ($referrals instanceof \Illuminate\Database\Eloquent\Builder) {
                // For query builders (level 1)
                $referrals = $referrals->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } else {
                // For collections (levels > 1)
                $referrals = $referrals->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }
        }
        
        return response()->json([
            'level' => $level,
            'referrals' => ($referrals instanceof \Illuminate\Database\Eloquent\Builder ? $referrals->get() : $referrals)->map(function($referral) {
                return [
                    'id' => $referral->id,
                    'name' => $referral->full_name,
                    'mobile_number' => $referral->mobile_number,
                    'status' => $referral->status,
                    'created_at' => $referral->created_at->format('Y-m-d H:i'),
                    'created_at_human' => $referral->created_at->diffForHumans()
                ];
            })
        ]);
    }
}