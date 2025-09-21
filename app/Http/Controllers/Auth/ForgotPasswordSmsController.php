<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class ForgotPasswordSmsController extends Controller
{
    public function showForm()
    {
        return view('auth.sms-forget-password'); // Create this view
    }

    public function sendSmsResetLink(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|digits:11'
        ]);

        $user = User::where('mobile_number', $request->mobile_number)->first();

        if (!$user) {
            return back()->withErrors(['mobile_number' => 'No account found for this mobile number.']);
        }

        $token = Str::random(6); // you can use Str::random(4) or mt_rand(100000, 999999)

        DB::table('mobile_password_resets')->updateOrInsert(
            ['mobile_number' => $user->mobile_number],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // TODO: Integrate your SMS provider here
        // e.g., SmsService::send($user->mobile_number, "Your reset code is: $token");

        return back()->with('status', 'A reset code has been sent to your mobile number.');
    }
}
