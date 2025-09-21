<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LoanPayment;

class LoanApprovalController extends Controller
{
public function index(Request $request)
{
    $query = Loan::with('member')->latest();

    if ($request->has('status') && in_array($request->status, ['Pending', 'Approved', 'Rejected'])) {
        $query->where('status', $request->status);
    }

    // Important: paginate() returns the $loans variable needed in the view
    $loans = $query->paginate(20);

    return view('admin.loans.index', compact('loans')); 
}

public function approve($id)
{
    $loan = Loan::findOrFail($id);

    if ($loan->status !== 'Pending') {
        return back()->with('error', 'Loan is already processed.');
    }

    $loan->status = 'Approved';
    $loan->save();

    // Credit wallet
    $loan->member->wallet->credit($loan->amount, 'Loan approved');

    // Generate payment schedule
    $monthlyDue = $loan->monthly_due;
    $startDate = Carbon::now()->addMonth();

    for ($i = 0; $i < $loan->term_months; $i++) {
        LoanPayment::create([
            'loan_id' => $loan->id,
            'due_date' => $startDate->copy()->addMonths($i),
            'amount' => $monthlyDue,
        ]);
    }

    return back()->with('success', 'Loan approved and scheduled.');
}

    public function reject(Loan $loan)
    {
        $loan->update(['status' => 'Rejected']);
        return back()->with('success', 'Loan rejected.');
    }

    private function calculateMonthly($amount, $rate)
    {
        $months = 12; // default 12 months
        $interest = ($amount * ($rate / 100));
        return round(($amount + $interest) / $months, 2);
    }

    public function show($id)
{
   $loan = \App\Models\Loan::with(['member', 'payments'])->findOrFail($id);
   return view('admin.loans.payments', compact('loan'));
}


}
