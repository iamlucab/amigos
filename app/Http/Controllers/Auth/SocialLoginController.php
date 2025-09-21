<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class SocialLoginController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirectToProvider($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Social login service is currently unavailable.');
        }
    }

    /**
     * Handle social provider callback
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if user exists by email
            $user = User::where('email', $socialUser->getEmail())->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
                
                // Check if status is pending
                if ($user->status === 'Pending') {
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Your registration is still pending approval by the admin.');
                }
                
                // Redirect based on role
                return $this->redirectBasedOnRole($user);
            } else {
                // Create new user and member
                return $this->createNewSocialUser($socialUser, $provider);
            }
            
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Social login failed. Please try again or use regular login.');
        }
    }

    /**
     * Create new user from social login
     */
    private function createNewSocialUser($socialUser, $provider)
    {
        try {
            // Extract name parts
            $fullName = $socialUser->getName() ?? $socialUser->getNickname() ?? 'User';
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';
            
            // Create member first
            $member = Member::create([
                'first_name' => ucfirst(strtolower($firstName)),
                'middle_name' => '',
                'last_name' => ucfirst(strtolower($lastName)),
                'birthday' => now()->subYears(25)->format('Y-m-d'), // Default age
                'mobile_number' => null, // Will need to be filled later
                'occupation' => 'Not specified',
                'address' => 'Not specified',
                'status' => 'Active', // Auto-approve social logins
                'role' => 'member',
                'sponsor_id' => null,
                'loan_eligible' => false,
            ]);
            
            // Create user
            $user = User::create([
                'name' => $fullName,
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(32)), // Random password
                'mobile_number' => null, // Will need to be filled later
                'role' => 'Member',
                'member_id' => $member->id,
                'status' => 'Active',
                'email_verified_at' => now(), // Auto-verify social logins
            ]);
            
            // Log the user in
            Auth::login($user);
            
            // Redirect to profile completion or dashboard
            return redirect()->route('member.dashboard')
                ->with('success', "Welcome! Please complete your profile by adding your mobile number and other details.");
            
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to create account. Please try regular registration.');
        }
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Staff':
                return redirect()->route('staff.dashboard');
            case 'Member':
                return redirect()->route('member.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    }
}