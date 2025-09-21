<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class SmsForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.sms-forget-password');
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

        $token = rand(100000, 999999); // numeric token

        DB::table('mobile_password_resets')->updateOrInsert(
            ['mobile_number' => $user->mobile_number],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // 🟡 TODO: Integrate your SMS gateway service here
        // SmsService::send($user->mobile_number, "Your reset code is: $token");

        return redirect()->route('password.sms.verify.form')->with([
            'mobile_number' => $user->mobile_number,
            'status' => 'A reset code has been sent to your mobile number.'
        ]);
    }

    public function showVerifyForm()
    {
        return view('auth.sms-verify-code'); // create this view
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|digits:11',
            'token' => 'required|digits:6'
        ]);

        $record = DB::table('mobile_password_resets')
            ->where('mobile_number', $request->mobile_number)
            ->where('token', $request->token)
            ->first();

        if (!$record || Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return back()->withErrors(['token' => 'Invalid or expired code.']);
        }

        return redirect()->route('password.sms.reset.form', ['mobile_number' => $request->mobile_number, 'token' => $request->token]);
    }

    public function showResetForm(Request $request)
    {
        return view('auth.sms-reset-password', [
            'mobile_number' => $request->mobile_number,
            'token' => $request->token
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|digits:11',
            'token' => 'required|digits:6',
            'password' => 'required|confirmed|min:6'
        ]);

        $record = DB::table('mobile_password_resets')
            ->where('mobile_number', $request->mobile_number)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Invalid token.']);
        }

        $user = User::where('mobile_number', $request->mobile_number)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('mobile_password_resets')->where('mobile_number', $request->mobile_number)->delete(); // cleanup

        return redirect()->route('login')->with('status', 'Password successfully reset. You can now login.');
    }
}
