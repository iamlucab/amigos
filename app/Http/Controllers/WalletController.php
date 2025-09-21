<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Member;
use App\Models\CashInRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    /**
     * Show the main wallet overview.
     */
    public function index()
    {
        $member = Auth::user()->member;
        $wallet = $member->wallet;

        if (!$wallet) {
            return redirect()->back()->withErrors(['wallet' => 'Wallet not found for this user.']);
        }

        $transactions = $wallet->transactions()->latest()->paginate(10);

        return view('wallet.index', compact('wallet', 'transactions'));
    }

    /**
     * Unified wallet transaction history (main + cashback).
     */
    public function history(Request $request)
    {
        $member = Auth::user()->member;

        if (!$member) {
            abort(403, 'No member profile found.');
        }

        $query = DB::table('wallet_transactions')
            ->join('wallets', 'wallets.id', '=', 'wallet_transactions.wallet_id')
            ->where('wallets.member_id', $member->id)
            ->when($request->type, function ($q) use ($request) {
                return $q->where('wallets.type', $request->type); // main or cashback
            })
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('wallet_transactions.created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('wallet_transactions.created_at', '<=', $request->end_date);
            })
            ->select('wallet_transactions.*', 'wallets.type as wallet_type')
            ->orderByDesc('wallet_transactions.created_at');

        $transactions = $query->paginate(15)->withQueryString();

        return view('wallet.history', [
            'transactions' => $transactions,
            'title' => 'Wallet History'
        ]);
    }

    /**
     * Transfer funds to another member.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'mobile_number' => [
                'required',
                'exists:members,mobile_number',
                function ($attribute, $value, $fail) {
                    if ($value === Auth::user()->member->mobile_number) {
                        $fail('You cannot send to your own account.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:1',
        ]);

        $sender = Auth::user()->member;
        $recipient = Member::where('mobile_number', $request->mobile_number)->firstOrFail();

        $amount = $request->amount;
        $senderWallet = $sender->wallet;
        $recipientWallet = $recipient->wallet ?? $recipient->wallet()->create([
            'wallet_id' => Wallet::generateWalletId(),
            'balance' => 0,
        ]);

        if ($senderWallet->balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient balance.'])->withInput(['_modal' => 'send']);
        }

        if ($amount > 10000) {
            return back()->withErrors(['amount' => 'Transfers above ₱10,000 require admin approval.'])->withInput(['_modal' => 'send']);
        }

        DB::beginTransaction();

        try {
            WalletTransaction::debitFrom(
                $senderWallet,
                $amount,
                'Transfer to ' . $recipient->mobile_number,
                $recipient->id,
                'transfer'
            );

            WalletTransaction::creditTo(
                $recipientWallet,
                $amount,
                'Received from ' . $sender->mobile_number,
                $sender->id,
                'transfer'
            );

            DB::commit();
            
            // Debug logging
            \Log::info('Transfer successful, setting success message', [
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'amount' => $amount
            ]);
            
            return redirect()->route('member.dashboard')->with('success', 'Wallet credits sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['amount' => 'Transfer failed: ' . $e->getMessage()])->withInput(['_modal' => 'send']);
        }
    }

    /**
     * Submit a cash-in request with optional file proof.
     */
    public function cashin(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:GCash,Bank,Others',
            'description' => 'nullable|string|max:255',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf,gif|max:2048',
            'note' => 'nullable|string|max:255',
            
        ]);

        $proofPath = $this->handleProofUpload($request);

        $cashIn = CashInRequest::create([
            'member_id' => Auth::user()->member->id,
            'amount' => $request->amount,
            'note' => $request->note,
            'description' => $request->description,
            'payment_method' => $request->payment_method,
            'proof_path' => $proofPath,
            'status' => 'Pending',
        ]);

        if ($cashIn) {
            Log::info('✅ Cash in saved!', ['id' => $cashIn->id, 'member_id' => Auth::user()->member->id, 'amount' => $request->amount]);
            
            // Debug logging for success message
            \Log::info('Cash in successful, setting success message', [
                'cash_in_id' => $cashIn->id,
                'member_id' => Auth::user()->member->id,
                'amount' => $request->amount
            ]);
            
            return redirect()->route('member.dashboard')->with('success', 'Cash in request submitted successfully! Please wait for admin approval.');
        }

        Log::error('Cash in request failed to save', ['member_id' => Auth::user()->member->id, 'amount' => $request->amount]);
        return back()->with('error', 'Failed to submit cash in request. Please try again.');
    }

    /**
     * Admin: Credit member's wallet.
     */
    public function adminTopUp(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:main,cashback',
            'note' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ])->validate([
        ]);

        $member = Member::findOrFail($request->member_id);
        $wallet = $request->type === 'cashback' ? $member->cashbackWallet : $member->wallet;

        if (!$wallet) {
            return back()->withErrors(['wallet' => 'Wallet not found.']);
        }

        $wallet->credit($request->amount, $request->note ?? 'Admin top-up');

        return back()->with('success', 'Wallet credited successfully.');
    }

    /**
     * Handle proof upload for cash-in.
     */
    protected function handleProofUpload(Request $request): ?string
    {
        if (!$request->hasFile('proof')) return null;

        $file = $request->file('proof');
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $destination = public_path('storage/proofs');

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return 'proofs/' . $filename;
    }



 public function transferCashbackToMain(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $member = auth()->user()->member;
        $cashbackWallet = $member->cashbackWallet;
        $mainWallet = $member->wallet;

        if (!$cashbackWallet || !$mainWallet) {
            return back()->with('error', 'Wallet not found.');
        }

        $amount = $request->amount;
        $feePercentage = Setting::get('wallet_transfer_fee', 10) / 100; // Default to 10% if not set
        $fee = round($amount * $feePercentage, 2);
        $netAmount = $amount - $fee;

        if ($cashbackWallet->balance < $amount) {
            return back()->with('error', 'Insufficient cashback balance.');
        }

        DB::beginTransaction();
        try {
            // Deduct from cashback
            $cashbackWallet->balance -= $amount;
            $cashbackWallet->save();

            // Credit to main
            $mainWallet->balance += $netAmount;
            $mainWallet->save();

            // Log cashback deduction
            WalletTransaction::create([
                'wallet_id' => $cashbackWallet->id,
                'member_id' => $member->id,
                'amount' => -$amount,
                'description' => "Transfer to main wallet (-₱" . number_format($fee, 2) . " fee)",
            ]);

            // Log main credit
            WalletTransaction::create([
                'wallet_id' => $mainWallet->id,
                'member_id' => $member->id,
                'amount' => $netAmount,
                'description' => "Received from cashback wallet (₱" . number_format($amount, 2) . " - ₱" . number_format($fee, 2) . " fee)",
            ]);

            DB::commit();
            return back()->with('success', 'Cashback transferred successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Transfer failed. ' . $e->getMessage());
        }
    }

    /**
     * Show payment request form when QR code is scanned
     */
    public function showPaymentRequest($walletId)
    {
        // Find the recipient by wallet ID
        $recipientWallet = Wallet::where('wallet_id', $walletId)->first();
        
        if (!$recipientWallet) {
            return redirect()->route('member.dashboard')->with('error', 'Invalid payment request. Wallet not found.');
        }

        $recipient = $recipientWallet->member;
        $sender = Auth::user()->member;

        // Prevent self-payment
        if ($sender->id === $recipient->id) {
            return redirect()->route('member.dashboard')->with('error', 'You cannot send money to yourself.');
        }

        return view('wallet.payment-request', [
            'recipient' => $recipient,
            'recipientWallet' => $recipientWallet,
            'senderWallet' => $sender->wallet
        ]);
    }

    /**
     * Process payment request from QR code scan
     */
    public function processPaymentRequest(Request $request, $walletId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'confirm' => 'required|accepted'
        ]);

        $recipientWallet = Wallet::where('wallet_id', $walletId)->first();
        
        if (!$recipientWallet) {
            return back()->withErrors(['error' => 'Invalid payment request. Wallet not found.']);
        }

        $sender = Auth::user()->member;
        $recipient = $recipientWallet->member;
        $amount = $request->amount;
        $senderWallet = $sender->wallet;

        // Prevent self-payment
        if ($sender->id === $recipient->id) {
            return back()->withErrors(['error' => 'You cannot send money to yourself.']);
        }

        // Check balance
        if ($senderWallet->balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient balance.'])->withInput();
        }

        DB::beginTransaction();

        try {
            // Debit from sender
            WalletTransaction::debitFrom(
                $senderWallet,
                $amount,
                'QR Payment to ' . $recipient->mobile_number,
                $recipient->id,
                'qr_payment'
            );

            // Credit to recipient
            WalletTransaction::creditTo(
                $recipientWallet,
                $amount,
                'QR Payment from ' . $sender->mobile_number,
                $sender->id,
                'qr_payment'
            );

            DB::commit();
            
            return redirect()->route('member.dashboard')->with('success',
                'Payment of ₱' . number_format($amount, 2) . ' sent successfully to ' . $recipient->full_name . '!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR Payment failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Payment failed. Please try again.');
        }
    }


}




