<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;


class SettingsController extends Controller
{

public function index()
{
    $settings = Setting::pluck('value', 'key')->toArray();
    return view('admin.settings.index', compact('settings'));
}

public function update(Request $request)
{
    $validated = $request->validate([
        'shipping_fee' => 'required|numeric|min:0',
        'promo_note' => 'nullable|string|max:255',
        'discount_rate' => 'nullable|numeric|min:0|max:100',
        'wallet_transfer_fee' => 'required|numeric|min:0|max:100',
        'membership_code_price' => 'required|numeric|min:0',
    ]);

    foreach ($validated as $key => $value) {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    return back()->with('success', 'Settings updated successfully.');
}

}
