<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ReferralBonusLog;
use App\Models\ReferralConfiguration;
use App\Services\ReferralBonusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Loan;

class MembersController extends Controller
{
    public function index()
    {
        $members = Member::with(['sponsor', 'membershipCode'])->get(); // eager load sponsor and membership code
        return view('members.index', compact('members'));
    }

    public function create()
    {
        $sponsors = Member::all();
        return view('members.create', compact('sponsors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'birthday'      => 'required|date',
            'mobile_number' => 'required|unique:members,mobile_number',
            'occupation'    => 'nullable|string|max:255',
            'photo'         => 'nullable|image|max:2048',
            'role'          => 'required|in:Admin,Staff,Member',
            'sponsor_id'    => 'nullable|exists:members,id',
            'status'        => 'required|in:Pending,Approved',
            'membership_code' => [
                'required',
                'string',
                'exists:membership_codes,code',
                function ($attribute, $value, $fail) {
                    $code = \App\Models\MembershipCode::where('code', $value)->first();
                    if (!$code || $code->used) {
                        $fail('The membership code is invalid or already used.');
                    }
                },
            ],
        ]);

        // Save photo to storage/photos
        if ($request->hasFile('photo')) {
            $filename = uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move(storage_path('photos'), $filename);
            $validated['photo'] = $filename;
        }

        $validated['loan_eligible'] = $request->has('loan_eligible');

        // Capitalize first letter of specified fields
        $validated['first_name'] = ucfirst(strtolower($validated['first_name']));
        $validated['middle_name'] = $validated['middle_name'] ? ucfirst(strtolower($validated['middle_name'])) : null;
        $validated['last_name'] = ucfirst(strtolower($validated['last_name']));
        $validated['occupation'] = $validated['occupation'] ? ucfirst(strtolower($validated['occupation'])) : null;

        // Create member
        $member = Member::create($validated);

        // Create user account
        $user = \App\Models\User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['mobile_number'] . '@amigostayo.com',
            'mobile_number' => $validated['mobile_number'],
            'password' => \Hash::make('secret123'), // Default password
            'role' => $validated['role'],
            'member_id' => $member->id,
            'status' => $validated['status'],
        ]);

        // Mark membership code as used
        $code = \App\Models\MembershipCode::where('code', $request->membership_code)->first();
        if ($code) {
            $code->markAsUsed($user->id);
        }

        // Award referral bonuses if member is created with "Approved" status
        if ($validated['status'] === 'Approved') {
            ReferralBonusService::awardReferralBonuses($member);
        }

        return redirect()->route('members.index')->with('success', 'Member created successfully.');
    }

    public function edit(Member $member)
    {
        $sponsors = Member::all();
        return view('members.edit', compact('member', 'sponsors'));
    }

 public function update(Request $request, Member $member)
{
    $rules = [
        'first_name'    => 'required|string|max:255',
        'middle_name'   => 'nullable|string|max:255',
        'last_name'     => 'required|string|max:255',
        'birthday'      => 'required|date',
        'mobile_number' => 'required|unique:members,mobile_number,' . $member->id,
        'email'         => 'nullable|email|unique:users,email,' . ($member->user->id ?? 'NULL'),
        'new_password'  => 'nullable|string|min:6',
        'occupation'    => 'nullable|string|max:255',
        'address'       => 'nullable|string|max:255',
        'photo'         => 'nullable|image|max:2048',
        'role'          => 'required|in:Admin,Staff,Member',
        'sponsor_id'    => 'nullable|exists:members,id',
        'status'        => 'required|in:Pending,Approved',
        'membership_code' => [
            'required',
            'string',
            'exists:membership_codes,code',
            function ($attribute, $value, $fail) use ($member) {
                $code = \App\Models\MembershipCode::where('code', $value)->first();

                // If the member already has this code, it's valid
                $currentCode = $member->membershipCode ? $member->membershipCode->code : null;
                if ($value === $currentCode) {
                    return;
                }

                // Otherwise, check if the code is unused
                if (!$code || $code->used) {
                    $fail('The membership code is invalid or already used.');
                }
            },
        ],
    ];

    $validated = $request->validate($rules);

    // Handle photo update
    if ($request->hasFile('photo')) {
        if ($member->photo) {
            $oldPath = storage_path('photos/' . $member->photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $filename = uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();
        $request->file('photo')->move(storage_path('photos'), $filename);
        $validated['photo'] = $filename;
    }

    $validated['loan_eligible'] = $request->has('loan_eligible');

    // Capitalize first letter of specified fields
    $validated['first_name'] = ucfirst(strtolower($validated['first_name']));
    $validated['middle_name'] = $validated['middle_name'] ? ucfirst(strtolower($validated['middle_name'])) : null;
    $validated['last_name'] = ucfirst(strtolower($validated['last_name']));
    $validated['occupation'] = $validated['occupation'] ? ucfirst(strtolower($validated['occupation'])) : null;
    $validated['address'] = $validated['address'] ? ucfirst(strtolower($validated['address'])) : null;

    $oldStatus = $member->status;
    $member->update($validated);

    // ✅ Ensure associated user is updated too
    $user = $member->user ?? \App\Models\User::where('member_id', $member->id)->first();

    if ($user) {
        $updateData = [
            'status'        => $validated['status'],
            'role'          => $validated['role'],
            'mobile_number' => $validated['mobile_number'],
            'name'          => $validated['first_name'] . ' ' . $validated['last_name'],
            'member_id'     => $member->id,
        ];

        // Update email if provided
        if (!empty($validated['email'])) {
            $updateData['email'] = $validated['email'];
        }

        // Update password if provided
        if (!empty($validated['new_password'])) {
            $updateData['password'] = Hash::make($validated['new_password']);
        }

        $user->update($updateData);
    }

    // Handle membership code
    $currentCode = $member->membershipCode ? $member->membershipCode->code : null;

    // If the code is different from the current one, update it
    if ($request->membership_code !== $currentCode) {
        // If there's a current code, release it
        if ($currentCode) {
            \App\Models\MembershipCode::where('code', $currentCode)
                ->update(['used' => false, 'used_by' => null, 'used_at' => null]);
        }

        // Assign the new code
        $code = \App\Models\MembershipCode::where('code', $request->membership_code)->first();
        if ($code && $user) {
            $code->update([
                'used' => true,
                'used_by' => $user->id,
                'used_at' => now(),
            ]);
        }
    }

    // ✅ If status changed from Pending to Approved, credit referral bonuses
    if ($oldStatus === 'Pending' && $validated['status'] === 'Approved' && $member->sponsor) {
        // Check if bonuses haven't been awarded yet to prevent duplicates
        if (!ReferralBonusService::bonusesAlreadyAwarded($member)) {
            ReferralBonusService::awardReferralBonuses($member);
        }
    }

    return redirect()->route('members.index')->with('success', 'Member updated.');
}



    public function show($id)
    {
        $member = Member::with('sponsor', 'user')->findOrFail($id);
        return view('admin.members.show', compact('member'));
    }

    public function destroy(Member $member)
    {
        if ($member->photo) {
            $photoPath = storage_path('photos/' . $member->photo);
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted.');
    }
}
