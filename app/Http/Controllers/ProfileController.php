<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $member = Auth::user()->member;
        return view('profile.edit', compact('member'));
    }

   public function update(Request $request)
{
    $user = Auth::user();
    $member = $user->member;

    $request->validate([
        'first_name'     => 'required|string|max:255',
        'middle_name'    => 'nullable|string|max:255',
        'last_name'      => 'required|string|max:255',
        'birthday'       => 'required|date',
        'mobile_number'  => 'required|string|max:255|unique:members,mobile_number,' . $member->id . '|unique:users,mobile_number,' . $user->id,
        'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
        'occupation'     => 'nullable|string|max:255',
        'address'        => 'nullable|string|max:255',
        'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'password'       => 'nullable|string|min:6|confirmed',
    ]);

    // ✅ Update MEMBER
    $member->fill($request->only([
        'first_name', 'middle_name', 'last_name', 'birthday',
        'mobile_number', 'occupation', 'address'
    ]));

 if ($request->hasFile('photo')) {
    $photo = $request->file('photo');
    $filename = uniqid() . '.' . $photo->getClientOriginalExtension();

    
    $photo->move(public_path('storage/photos'), $filename);

    $member->photo = $filename;
}
    $member->save();

    // ✅ Update USER
    $user->mobile_number = $request->mobile_number;
    $user->email = $request->email;
    $user->name = $request->first_name . ' ' . $request->last_name;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'Profile updated successfully!');
}
}
