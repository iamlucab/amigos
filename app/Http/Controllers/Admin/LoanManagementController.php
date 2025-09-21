<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanManagementController extends Controller
{
    /**
     * Display a listing of loan requests with filtering options.
     */
    public function index(Request $request)
    {
        $query = Loan::with('member')->latest();

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['Pending', 'Approved', 'Rejected', 'Cancelled'])) {
            $query->where('status', $request->status);
        }

        // Filter by member name if provided
        if ($request->has('member_name') && !empty($request->member_name)) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->member_name . '%');
            });
        }

        // Filter by date range if provided
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $loans = $query->paginate(15);
        
        // Get counts for dashboard widgets
        $pendingCount = Loan::where('status', 'Pending')->count();
        $approvedCount = Loan::where('status', 'Approved')->count();
        $rejectedCount = Loan::where('status', 'Rejected')->count();

        return view('admin.loans.management', compact('loans', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Display the specified loan details.
     */
    public function show($id)
    {
        $loan = Loan::with(['member', 'payments'])->findOrFail($id);
        return view('admin.loans.show', compact('loan'));
    }

    /**
     * Approve a loan request.
     */
    public function approve($id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'Pending') {
            return back()->with('error', 'Only pending loans can be approved.');
        }

        // Calculate monthly payment
        $total = $loan->amount * (1 + ($loan->interest_rate / 100));
        $monthlyPayment = round($total / $loan->term_months, 2);
        
        $loan->monthly_payment = $monthlyPayment;
        $loan->status = 'Approved';
        $loan->approved_at = now();
        $loan->save();

        // Credit the member's wallet
        $wallet = $loan->member->wallet;
        if ($wallet) {
            $wallet->credit($loan->amount, 'Loan approved');
        }

        // Generate payment schedule
        $startDate = Carbon::now()->addMonth()->startOfMonth();
        
        for ($i = 0; $i < $loan->term_months; $i++) {
            LoanPayment::create([
                'loan_id' => $loan->id,
                'amount' => $monthlyPayment,
                'due_date' => $startDate->copy()->addMonths($i),
                'is_paid' => false,
                'status' => 'Pending'
            ]);
        }

        return back()->with('success', 'Loan approved and payment schedule generated.');
    }

    /**
     * Reject a loan request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'Pending') {
            return back()->with('error', 'Only pending loans can be rejected.');
        }

        $loan->status = 'Rejected';
        $loan->rejection_reason = $request->rejection_reason;
        $loan->rejected_at = now();
        $loan->save();

        return back()->with('success', 'Loan request rejected.');
    }

    /**
     * Generate loan reports.
     */
    public function reports(Request $request)
    {
        $query = Loan::with(['member', 'payments'])->where('status', 'Approved');

        // Filter by date range if provided
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $loans = $query->get();

        // Calculate summary statistics
        $totalLoaned = $loans->sum('amount');
        $totalInterest = $loans->sum(function ($loan) {
            return $loan->amount * ($loan->interest_rate / 100);
        });
        $totalPayable = $totalLoaned + $totalInterest;
        $totalPaid = $loans->sum(function ($loan) {
            return $loan->payments->where('is_paid', true)->sum('amount');
        });
        $totalOutstanding = $totalPayable - $totalPaid;

        return view('admin.loans.reports', compact(
            'loans', 
            'totalLoaned', 
            'totalInterest', 
            'totalPayable', 
            'totalPaid', 
            'totalOutstanding'
        ));
    }
}