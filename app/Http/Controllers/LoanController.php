<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{

public function index()
{
    $member = Auth::user()->member;

    if (!$member) {
        return redirect()->back()->withErrors(['error' => 'Member profile not found.']);
    }

    $loans = $member->loans()->latest()->get();

    return view('loans.index', compact('loans'));
}

    public function requestLoan(Request $request)
    {
       $request->validate([
    'amount' => 'required|numeric|min:100',
    'term_months' => 'required|in:6,12',
    'purpose' => 'nullable|string|max:255',
]);

        $member = Auth::user()->member;
        
        // Check if member is eligible for loans
        if (!$member->loan_eligible) {
            return back()->withErrors(['error' => 'You are not eligible for loans.']);
        }

    $loan = Loan::create([
    'member_id' => $member->id,
    'amount' => $request->amount,
    'purpose' => $request->purpose,
    'status' => 'Pending',
    'monthly_payment' => 0,
    'interest_rate' => 2, // Assuming a fixed interest rate of 2%
    'term_months' => $request->term_months,
]);

        // Set toast notification for loan request submission
        return back()->with('success', 'Loan request submitted successfully!')
                    ->with('toast', [
                        'type' => 'success',
                        'title' => 'Loan Request Submitted',
                        'message' => 'Your loan request for ₱' . number_format($request->amount, 2) . ' has been submitted and is pending approval.'
                    ]);
    }

    public function cancel($id)
{
    $loan = Loan::where('id', $id)
        ->where('member_id', Auth::user()->member->id)
        ->where('status', 'Pending')
        ->firstOrFail();

    $loan->status = 'Cancelled';
    $loan->save();

    return back()->with('success', 'Loan request cancelled.');
}

public function approve($id)
{
    $loan = Loan::findOrFail($id);

    if ($loan->status !== 'Pending') {
        return back()->with('error', 'Only pending loans can be approved.');
    }

    // Optional: Monthly payment calculation
    $total = $loan->amount * 1.05;
    $monthlyPayment = $total / $loan->term_months;
    $loan->monthly_payment = round($monthlyPayment, 2);
    $loan->status = 'Approved';

    // Credit wallet
    $wallet = $loan->member->wallet;
    if ($wallet) {
        $wallet->credit($loan->amount, 'Loan approved');
    }

    $loan->save();

    return back()->with('success', 'Loan approved and amount credited to wallet.');
}


public function show($id)
{
    $member = Auth::user()->member;

    $loan = Loan::with('payments') // Make sure `payments` relation exists
        ->where('member_id', $member->id)
        ->findOrFail($id);

    return view('loans.show', compact('loan'));
}


public function paymentHistory()
{
    $memberId = auth()->user()->member->id;

    $payments = LoanPayment::whereHas('loan', function ($q) use ($memberId) {
        $q->where('member_id', $memberId)->where('status', 'Approved');
    })->with('loan')->orderBy('due_date')->get();

    return view('loans.payment-history', compact('payments'));
}


}
