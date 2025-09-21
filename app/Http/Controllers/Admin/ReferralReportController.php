<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class ReferralReportController extends Controller
{
   public function index(Request $request)
{
    $query = WalletTransaction::where('description', 'LIKE', '%referral bonus%')
        ->with('wallet.member');

    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    $transactions = $query->latest()->get(); // using get() for DataTables

    return view('admin.reports.referral', compact('transactions'));
}
}
