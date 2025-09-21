<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashInRequest;
use Illuminate\Http\Request;

class CashInApprovalController extends Controller
{
    public function index()
    {
        $requests = CashInRequest::with('member')->latest()->get();
        return view('admin.wallet.cashin-approvals', compact('requests'));
    }

    public function approve($id)
    {
        $request = CashInRequest::with('member.wallet')->findOrFail($id);

        $request->update(['status' => 'Approved']);

        $member = $request->member;

        if (!$member) {
            return back()->with('error', 'Member not found.');
        }

        $wallet = $member->wallet;

        if (!$wallet) {
            $wallet = $member->wallet()->create([
                'balance' => 0,
                'wallet_id' => \App\Models\Wallet::generateWalletId(),
                'member_id' => $member->id,
                'user_id' => $member->user_id,
            ]);
        }

        $wallet->credit($request->amount, 'Cash In Approved');

        return back()->with('success', 'Cash In approved and wallet credited.');
    }

    public function reject($id)
    {
        $request = CashInRequest::findOrFail($id);
        $request->update(['status' => 'Rejected']);

        return back()->with('success', 'Cash In rejected.');
    }

    public function markAsReviewed($id)
    {
        $request = CashInRequest::findOrFail($id);

        if ($request->status !== 'Pending') {
            return back()->with('error', 'Only pending requests can be marked as reviewed.');
        }

        $request->update(['status' => 'Reviewed']);

        return back()->with('success', 'Cash In request marked as reviewed.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Reviewed,Approved,Rejected',
        ]);

        $cashIn = CashInRequest::with('member.wallet')->findOrFail($id);

        if ($cashIn->status === 'Approved' && $request->status === 'Approved') {
            return back()->with('info', 'Already approved.');
        }

        $cashIn->update(['status' => $request->status]);

        if ($request->status === 'Approved') {
            $member = $cashIn->member;

            if (!$member) {
                return back()->with('error', 'Member not found.');
            }

            $wallet = $member->wallet;

            if (!$wallet) {
                $wallet = $member->wallet()->create([
                    'balance' => 0,
                    'wallet_id' => \App\Models\Wallet::generateWalletId(),
                    'member_id' => $member->id,
                    'user_id' => $member->user_id,
                ]);
            }

            $wallet->credit($cashIn->amount, 'Cash In Approved');
        }

        return back()->with('success', "Cash In status updated to '{$request->status}'.");
    }
}
