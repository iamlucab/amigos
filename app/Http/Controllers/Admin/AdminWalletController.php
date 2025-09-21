<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class AdminWalletController extends Controller
{
    /**
     * Display top-up/refund form and recent transactions.
     */
    public function topupForm(Request $request)
    {
        $member = null;
        $transactions = collect();

        if ($request->filled('mobile_number')) {
            $member = Member::where('mobile_number', $request->mobile_number)->first();

            if ($member) {
                $wallet = $request->wallet_type === 'cashback'
                    ? $member->cashbackWallet
                    : $member->wallet;

                if ($wallet) {
                    $transactions = $wallet->transactions()->latest()->take(10)->get();
                }
            }
        }

        return view('admin.wallet.topup', compact('member', 'transactions'));
    }

    /**
     * Process top-up or refund to a member's wallet.
     */
    public function processTopup(Request $request)
    {
        $validated = $request->validate([
            'mobile_number' => 'required|exists:members,mobile_number',
            'wallet_type'   => 'required|in:main,cashback',
            'type'          => 'required|in:topup,refund',
            'amount'        => 'required|numeric|min:1',
            'note'          => 'nullable|string|max:255',
        ]);

        $member = Member::where('mobile_number', $validated['mobile_number'])->firstOrFail();

        $wallet = $validated['wallet_type'] === 'cashback'
            ? $member->cashbackWallet
            : $member->wallet;

        // Auto-create wallet if missing
        if (!$wallet) {
            $wallet = $validated['wallet_type'] === 'cashback'
                ? $member->cashbackWallet()->create([
                    'wallet_id' => \App\Models\Wallet::generateWalletId(),
                    'balance' => 0,
                    'member_id' => $member->id,
                    'user_id' => $member->user_id ?? null,
                ])
                : $member->wallet()->create([
                    'wallet_id' => \App\Models\Wallet::generateWalletId(),
                    'balance' => 0,
                    'member_id' => $member->id,
                    'user_id' => $member->user_id ?? null,
                ]);
        }

        $description = ucfirst($validated['type']) . ' by Admin' . ($validated['note'] ? ' - ' . $validated['note'] : '');

        if ($validated['type'] === 'topup') {
            $wallet->credit($validated['amount'], $description);
        } else {
            $wallet->debit($validated['amount'], $description);
        }

        return redirect()->route('admin.wallet.topup', ['mobile_number' => $validated['mobile_number'], 'wallet_type' => $validated['wallet_type']])
            ->with('success', '✅ ' . ucfirst($validated['type']) . ' successful!');
    }
}
