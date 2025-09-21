<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class GuestRegistrationController extends Controller
{
    public function create()
    {
        return view('members.guest-register');
    }

    public function welcome()
    {
        return view('welcome-register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:191',
            'middle_name'   => 'nullable|string|max:191',
            'last_name'     => 'required|string|max:191',
            'birthday'      => 'required|date',
            'mobile_number' => 'required|string|max:11|regex:/^09\d{9}$/',
            'occupation'    => 'nullable|string|max:191',
            'photo'         => 'nullable|image|max:2048',
            'password'      => 'required|string|min:6',
            'payment_method' => 'required|string|in:GCash,Paymaya,Bank Transfer,Pay in Cash,Pay Later',
            'proof_of_payment' => 'nullable|image|max:2048',
        ]);

        // ✅ Check if mobile is already registered
        if (
            Member::where('mobile_number', $request->mobile_number)->exists() ||
            User::where('mobile_number', $request->mobile_number)->exists()
        ) {
            return back()->withErrors(['mobile_number' => 'Mobile number is already registered.'])->withInput();
        }

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('photos', 'public')
            : null;
            
        $proofOfPaymentPath = $request->hasFile('proof_of_payment')
            ? $request->file('proof_of_payment')->store('payment_proofs', 'public')
            : null;

        $member = Member::create([
            'first_name'    => ucfirst(strtolower($request->first_name)),
            'middle_name'   => $request->middle_name ? ucfirst(strtolower($request->middle_name)) : null,
            'last_name'     => ucfirst(strtolower($request->last_name)),
            'birthday'      => $request->birthday,
            'mobile_number' => $request->mobile_number,
            'occupation'    => $request->occupation ? ucfirst(strtolower($request->occupation)) : null,
            'photo'         => $photoPath,
            'payment_method' => $request->payment_method,
            'proof_of_payment' => $proofOfPaymentPath,
            'role'          => 'Member',
            'status'        => 'Pending',
            'sponsor_id'    => null, // Will be assigned by admin during approval
            'loan_eligible' => false,
        ]);

        User::create([
            'name'          => $member->first_name . ' ' . $member->last_name,
            'email'         => $request->mobile_number . '@amigostayo.com',
            'mobile_number' => $request->mobile_number,
            'password'      => Hash::make($request->password),
            'role'          => 'Member',
            'member_id'     => $member->id,
            'status'        => 'Pending',
        ]);

        return redirect('/welcome.php')->with('success', 'Registration submitted! Wait for admin approval.');
    }

    /**
     * Show referral registration form
     */
    public function createWithReferral($sponsor_id)
    {
        // Validate sponsor exists and is approved
        $sponsor = Member::where('id', $sponsor_id)
            ->where('status', 'Approved')
            ->first();

        if (!$sponsor) {
            return redirect()->route('guest.register')
                ->with('error', 'Invalid referral link. Please register normally.');
        }

        return view('members.referral-register', compact('sponsor', 'sponsor_id'));
    }

    /**
     * Store referral registration
     */
    public function storeWithReferral(Request $request, $sponsor_id)
    {
        $request->validate([
            'first_name'    => 'required|string|max:191',
            'middle_name'   => 'nullable|string|max:191',
            'last_name'     => 'required|string|max:191',
            'birthday'      => 'required|date',
            'mobile_number' => 'required|string|max:11|regex:/^09\d{9}$/',
            'occupation'    => 'nullable|string|max:191',
            'photo'         => 'nullable|image|max:2048',
            'password'      => 'required|string|min:6|confirmed',
            'sponsor_id'    => 'required|exists:members,id',
            'payment_method' => 'required|string|in:GCash,Paymaya,Bank Transfer,Pay in Cash,Pay Later',
            'proof_of_payment' => 'nullable|image|max:2048',
        ]);

        // Validate sponsor exists and is approved
        $sponsor = Member::where('id', $sponsor_id)
            ->where('status', 'Approved')
            ->first();

        if (!$sponsor) {
            return back()->withErrors(['sponsor_id' => 'Invalid sponsor. Please contact support.'])->withInput();
        }

        // ✅ Check if mobile is already registered
        if (
            Member::where('mobile_number', $request->mobile_number)->exists() ||
            User::where('mobile_number', $request->mobile_number)->exists()
        ) {
            return back()->withErrors(['mobile_number' => 'Mobile number is already registered.'])->withInput();
        }

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('photos', 'public')
            : null;
            
        $proofOfPaymentPath = $request->hasFile('proof_of_payment')
            ? $request->file('proof_of_payment')->store('payment_proofs', 'public')
            : null;

        $member = Member::create([
            'first_name'    => ucfirst(strtolower($request->first_name)),
            'middle_name'   => $request->middle_name ? ucfirst(strtolower($request->middle_name)) : null,
            'last_name'     => ucfirst(strtolower($request->last_name)),
            'birthday'      => $request->birthday,
            'mobile_number' => $request->mobile_number,
            'occupation'    => $request->occupation ? ucfirst(strtolower($request->occupation)) : null,
            'photo'         => $photoPath,
            'payment_method' => $request->payment_method,
            'proof_of_payment' => $proofOfPaymentPath,
            'role'          => 'Member',
            'status'        => 'Pending',
            'sponsor_id'    => $sponsor_id, // Automatically set the sponsor from referral link
            'loan_eligible' => false,
        ]);

        User::create([
            'name'          => $member->first_name . ' ' . $member->last_name,
            'email'         => $request->mobile_number . '@amigostayo.com',
            'mobile_number' => $request->mobile_number,
            'password'      => Hash::make($request->password),
            'role'          => 'Member',
            'member_id'     => $member->id,
            'status'        => 'Pending',
        ]);

        // Determine which route to redirect to based on the current request path
        $redirectRoute = request()->is('join/*')
            ? 'join.referral'
            : 'member.register.referral';
            
        return redirect()->route($redirectRoute, $sponsor_id)
            ->with('success', 'Registration submitted! You and your sponsor will receive bonuses once approved.');
    }
}
