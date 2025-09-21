<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipCodeRequest;
use App\Models\Setting;
use App\Models\Notification;

class MembershipCodeRequestController extends Controller
{
    public function create()
    {
        $member = auth()->user()->member;
        $user = auth()->user();
        $amountPerCode = Setting::where('key', 'membership_code_price')->value('value') ?? 100;
        $wallet = $member->wallet;
        
        // Get reserved membership codes for the member (only reserved and not used)
        $reservedCodes = \App\Models\MembershipCode::where('reserved_by', $user->id)
            ->where('reserved', true)
            ->where('used', false)
            ->orderBy('reserved_at', 'desc')
            ->get();

        // Get membership code requests for the member
        $membershipCodeRequests = \App\Models\MembershipCodeRequest::where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('members.membership-code-request.create', compact('member', 'amountPerCode', 'wallet', 'reservedCodes', 'membershipCodeRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'payment_method' => 'required|in:GCash,Bank,Wallet',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'note' => 'nullable|string|max:255'
        ]);

        $member = auth()->user()->member;
        $amountPerCode = Setting::where('key', 'membership_code_price')->value('value') ?? 100;
        $totalAmount = $request->quantity * $amountPerCode;
        
        // Handle wallet payment immediately
        if ($request->payment_method === 'Wallet') {
            $wallet = $member->wallet;
            if ($wallet->balance < $totalAmount) {
                return redirect()->back()->with('error', 'Insufficient wallet balance for this transaction.')->withInput();
            }
            
            // Deduct amount from wallet
            $wallet->update(['balance' => $wallet->balance - $totalAmount]);
            
            // Generate and reserve codes immediately
            $reservedCodes = $this->generateAndReserveCodes($member, $request->quantity);
            
            // Create membership code request for record keeping
            $membershipCodeRequest = MembershipCodeRequest::create([
                'member_id' => $member->id,
                'quantity' => $request->quantity,
                'amount_per_code' => $amountPerCode,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'approved',
                'proof_path' => null,
                'note' => 'Paid with wallet'
            ]);
            
            // Create request items to link the codes with the request
            foreach ($reservedCodes as $code) {
                \App\Models\MembershipCodeRequestItem::create([
                    'membership_code_request_id' => $membershipCodeRequest->id,
                    'membership_code_id' => $code->id
                ]);
            }
            
            return redirect()->route('member.dashboard')->with('success', 'Membership codes purchased successfully! The codes have been transferred to your account.');
        }

        // Handle file upload for other payment methods
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payment_proofs', 'public');
        }

        // Create membership code request for non-wallet payments
        $membershipCodeRequest = MembershipCodeRequest::create([
            'member_id' => $member->id,
            'quantity' => $request->quantity,
            'amount_per_code' => $amountPerCode,
            'total_amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'proof_path' => $proofPath,
            'note' => $request->note
        ]);

        // Create notification for admin
        Notification::createForAdmin(
            'membership_code_request',
            'New membership code request from ' . $member->full_name . ' for ' . $request->quantity . ' codes',
            $membershipCodeRequest
        );

        return redirect()->route('member.dashboard')->with('success', 'Membership code request submitted successfully. Please wait for admin approval.');
    }
    
    private function generateAndReserveCodes($member, $quantity)
    {
        $userId = $member->user->id;
        
        // Get available codes first
        $availableCodes = \App\Models\MembershipCode::available()->take($quantity)->get();
        
        // If we don't have enough available codes, generate more
        $neededCodes = $quantity - $availableCodes->count();
        if ($neededCodes > 0) {
            for ($i = 0; $i < $neededCodes; $i++) {
                $code = \App\Models\MembershipCode::generateCode();
                $availableCodes->push($code);
            }
        }
        
        // Reserve the codes for the member
        $reservedCodes = [];
        foreach ($availableCodes->take($quantity) as $code) {
            // Only reserve if not already reserved
            if (!$code->reserved) {
                $code->markAsReserved($userId);
            }
            $reservedCodes[] = $code;
        }
        
        return $reservedCodes;
    }

    public function index()
    {
        $member = auth()->user()->member;
        $requests = MembershipCodeRequest::where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('members.membership-code-request.index', compact('requests'));
    }
}
