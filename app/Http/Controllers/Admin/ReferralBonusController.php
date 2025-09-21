<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralBonusLog;
use Illuminate\Http\Request;

class ReferralBonusController extends Controller
{


 public function index(Request $request)
{
    $query = ReferralBonusLog::with(['member', 'referredMember'])->latest();

    if ($request->filled('sponsor')) {
        $query->whereHas('member', function ($q) use ($request) {
            $q->where('first_name', 'like', '%' . $request->sponsor . '%')
              ->orWhere('last_name', 'like', '%' . $request->sponsor . '%');
        });
    }

    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    // ✅ Clone before consuming with sum
    $totalBonusAmount = (clone $query)->sum('amount');

    // ✅ Then paginate original query
    $logs = $query->paginate(20);

    $topEarners = \App\Models\Member::withSum('referralBonusLogs', 'amount')
        ->orderByDesc('referral_bonus_logs_sum_amount')
        ->take(5)
        ->get();

    return view('admin.referral_bonuses.index', compact('logs', 'topEarners', 'totalBonusAmount'));
}



public function export(Request $request)
{
    $logs = ReferralBonusLog::with(['member', 'referredMember'])->latest()->get();

    $csv = "Date,Level,Sponsor,Referred,Amount,Description\n";

    foreach ($logs as $log) {
        $csv .= sprintf(
            "%s,%d,%s,%s,%.2f,%s\n",
            $log->created_at->format('Y-m-d H:i'),
            $log->level,
            $log->member->full_name ?? '',
            $log->referredMember->full_name ?? '',
            $log->amount,
            $log->description
        );
    }

    return response($csv)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="referral_bonuses.csv"');
}



}

