<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Exception;

class OtpLoginController extends Controller
{
    /**
     * Send OTP to mobile number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|size:11|regex:/^09[0-9]{9}$/',
        ]);

        $mobileNumber = $request->mobile_number;
        
        // Check if user exists
        $user = User::where('mobile_number', $mobileNumber)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered. Please register first.'
            ], 404);
        }

        // Check if user is active
        if ($user->status === 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is still pending approval.'
            ], 403);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        
        // Store OTP in cache for 5 minutes
        $cacheKey = "otp_login_{$mobileNumber}";
        Cache::put($cacheKey, $otp, now()->addMinutes(5));
        
        // Store attempt count to prevent spam
        $attemptKey = "otp_attempts_{$mobileNumber}";
        $attempts = Cache::get($attemptKey, 0);
        
        if ($attempts >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Please try again later.'
            ], 429);
        }
        
        Cache::put($attemptKey, $attempts + 1, now()->addMinutes(15));
        
        // Send SMS (integrate with your SMS service)
        $smsResult = $this->sendSms($mobileNumber, "Your E-Bili login OTP: {$otp}. Valid for 5 minutes. Do not share this code.");
        
        if ($smsResult) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your mobile number.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP and login user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|size:11',
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ]);

        $mobileNumber = $request->mobile_number;
        $inputOtp = $request->otp;
        
        // Get cached OTP
        $cacheKey = "otp_login_{$mobileNumber}";
        $cachedOtp = Cache::get($cacheKey);
        
        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or invalid. Please request a new one.'
            ], 400);
        }
        
        if ($cachedOtp != $inputOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please check and try again.'
            ], 400);
        }
        
        // OTP is valid, find and login user
        $user = User::where('mobile_number', $mobileNumber)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }
        
        // Clear OTP from cache
        Cache::forget($cacheKey);
        Cache::forget("otp_attempts_{$mobileNumber}");
        
        // Login user
        Auth::login($user);
        $request->session()->regenerate();
        
        // Return success with redirect URL
        $redirectUrl = $this->getRedirectUrl($user);
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect_url' => $redirectUrl
        ]);
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl($user)
    {
        switch ($user->role) {
            case 'Admin':
                return route('admin.dashboard');
            case 'Staff':
                return route('staff.dashboard');
            case 'Member':
                return route('member.dashboard');
            default:
                return route('login');
        }
    }

    /**
     * Send SMS using your SMS service
     * Replace this with your actual SMS service integration
     */
    private function sendSms($mobileNumber, $message)
    {
        try {
            // Example using a generic SMS API
            // Replace with your actual SMS service (Semaphore, Twilio, etc.)
            
            // For now, we'll simulate SMS sending
            // In production, integrate with your SMS provider
            
            // Example for Semaphore SMS (Philippines)
            /*
            $response = Http::post('https://semaphore.co/api/v4/messages', [
                'apikey' => env('SEMAPHORE_API_KEY'),
                'number' => $mobileNumber,
                'message' => $message,
                'sendername' => env('SMS_SENDER_NAME', 'E-Bili')
            ]);
            
            return $response->successful();
            */
            
            // For development, log the OTP instead of sending SMS
            \Log::info("OTP for {$mobileNumber}: {$message}");
            
            return true; // Simulate successful SMS sending
            
        } catch (Exception $e) {
            \Log::error("SMS sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        return $this->sendOtp($request);
    }
}