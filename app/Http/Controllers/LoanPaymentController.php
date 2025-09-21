<?php

namespace App\Http\Controllers;

use App\Models\LoanPayment;
use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LoanPaymentController extends Controller
{
    /**
     * Admin: View breakdown of loan payments.
     */
    public function viewPayments($loanId)
    {
        $loan = Loan::with(['member', 'payments'])->findOrFail($loanId);
        return view('admin.loans.payments', compact('loan'));
    }

    /**
     * Admin: Manually mark a loan payment as paid.
     */
    public function markAsPaid($id)
    {
        $payment = LoanPayment::findOrFail($id);

        if ($payment->is_paid) {
            return back()->with('info', 'This payment is already marked as paid.');
        }

        $payment->update([
            'is_paid' => true,
            'paid_at' => now(),
            'payment_method' => 'Manual',
            'is_verified' => true,
        ]);

        return back()->with('success', 'Payment marked as paid.');
    }

    /**
     * Member: Pay a specific loan payment if they have enough balance.
     */
    public function payNow(Request $request, $id)
    {
        $payment = LoanPayment::findOrFail($id);
        $member = auth()->user()->member;

        // Check if already paid
        if ($payment->is_paid) {
            return back()->with('info', 'Payment already made.');
        }

        // Authorization
        if ($payment->loan->member_id !== $member->id) {
            abort(403, 'Unauthorized access to payment.');
        }

        // Validate payment method
        $request->validate([
            'payment_method' => 'required|in:Wallet,GCash,Bank,Others',
            'reference_number' => 'required_unless:payment_method,Wallet',
            'payment_proof' => 'required_unless:payment_method,Wallet|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // Process payment based on method
        if ($request->payment_method === 'Wallet') {
            // Wallet balance check
            $wallet = $member->wallet;
            if ($wallet->balance < $payment->amount) {
                return back()->withErrors(['wallet' => 'Insufficient wallet balance.']);
            }

            // Deduct from wallet and mark as paid
            $wallet->debit($payment->amount, 'Loan Payment for ' . $payment->due_date->format('F Y'));
            
            $payment->update([
                'is_paid' => true,
                'paid_at' => now(),
                'payment_method' => 'Wallet',
                'is_verified' => true, // Wallet payments are auto-verified
            ]);

            return back()->with('success', 'Loan payment successfully made through Wallet.')
                        ->with('toast', [
                            'type' => 'success',
                            'title' => 'Payment Successful',
                            'message' => 'Your loan payment of ₱' . number_format($payment->amount, 2) . ' has been processed.'
                        ]);
        } else {
            // Handle external payment methods (GCash, Bank, Others)
            $paymentProofPath = null;
            
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }
            
            $payment->update([
                'is_paid' => true,
                'paid_at' => now(),
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'payment_proof' => $paymentProofPath,
                'is_verified' => false, // External payments need verification
                'notes' => $request->notes,
            ]);

            return back()->with('success', 'Payment recorded and pending verification.')
                        ->with('toast', [
                            'type' => 'info',
                            'title' => 'Payment Pending Verification',
                            'message' => 'Your payment has been recorded and is awaiting verification by an admin.'
                        ]);
        }
    }

    /**
     * Admin: Verify a payment made through external methods.
     */
    public function verifyPayment($id)
    {
        $payment = LoanPayment::findOrFail($id);
        
        if (!$payment->is_paid) {
            return back()->with('error', 'Cannot verify an unpaid payment.');
        }
        
        if ($payment->is_verified) {
            return back()->with('info', 'This payment is already verified.');
        }
        
        $payment->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);
        
        return back()->with('success', 'Payment has been verified successfully.');
    }

    /**
     * Admin: Record a manual payment.
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:loan_payments,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:Wallet,GCash,Bank,Others',
            'reference_number' => 'nullable|string|max:255',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $payment = LoanPayment::findOrFail($request->payment_id);
        $paymentProofPath = null;
        
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }
        
        // Update the payment record
        $payment->update([
            'amount' => $request->amount,
            'is_paid' => true,
            'paid_at' => $request->paid_at,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'payment_proof' => $paymentProofPath,
            'notes' => $request->notes,
            'is_verified' => $request->payment_method === 'Wallet', // Auto-verify wallet payments
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Member: Show payment modal with multiple payment options.
     */
    public function showPaymentModal($id)
    {
        $payment = LoanPayment::with('loan.member')->findOrFail($id);
        $member = auth()->user()->member;
        
        // Authorization
        if ($payment->loan->member_id !== $member->id) {
            abort(403, 'Unauthorized access to payment.');
        }
        
        return view('loans.payment-modal', compact('payment'));
    }

}
