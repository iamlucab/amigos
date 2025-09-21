<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AliexpressToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AliexpressAuthController extends Controller
{
    /**
     * Redirect to AliExpress OAuth
     */
    public function redirectToProvider()
    {
        $clientId = config('services.aliexpress.client_id');
        $redirectUrl = config('services.aliexpress.redirect');
        
        // Generate a random state for security
        $state = Str::random(40);
        session(['aliexpress_oauth_state' => $state]);
        
        // Build the authorization URL with correct AliExpress parameters
        $authUrl = 'https://oauth.aliexpress.com/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUrl,
            'state' => $state,
        ]);
        
        return redirect($authUrl);
    }

    /**
     * Handle AliExpress OAuth callback
     */
    public function handleProviderCallback(Request $request)
    {
        // Verify state parameter to prevent CSRF
        $state = $request->input('state');
        if ($state !== session('aliexpress_oauth_state')) {
            return redirect()->route('login')->with('error', 'Invalid state parameter.');
        }
        
        // Clear the state from session
        session()->forget('aliexpress_oauth_state');
        
        // Check for errors
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'AliExpress authorization failed: ' . $request->input('error_description'));
        }
        
        // Get authorization code
        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('login')->with('error', 'Authorization code not received.');
        }
        
        // Exchange code for access token
        try {
            $tokenResponse = $this->exchangeCodeForToken($code);
            
            // Store token in database
            $this->storeToken($tokenResponse);
            
            return redirect()->route('login')->with('success', 'AliExpress account successfully connected!');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to connect AliExpress account: ' . $e->getMessage());
        }
    }

    /**
     * Exchange authorization code for access token
     */
    private function exchangeCodeForToken($code)
    {
        $clientId = config('services.aliexpress.client_id');
        $clientSecret = config('services.aliexpress.client_secret');
        $redirectUrl = config('services.aliexpress.redirect');
        
        $response = Http::asForm()->post('https://oauth.aliexpress.com/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUrl,
        ]);
        
        if ($response->failed()) {
            throw new \Exception('Failed to exchange code for token: ' . $response->body());
        }
        
        return $response->json();
    }

    /**
     * Store token in database
     */
    private function storeToken($tokenData)
    {
        // Get current user if authenticated
        $userId = Auth::check() ? Auth::id() : null;
        
        // Calculate expiration time
        $expiresAt = null;
        if (isset($tokenData['expires_in'])) {
            $expiresAt = now()->addSeconds($tokenData['expires_in']);
        }
        
        // Create or update token record
        AliexpressToken::updateOrCreate(
            ['user_id' => $userId], // Match criteria
            [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'token_type' => $tokenData['token_type'] ?? 'Bearer',
                'expires_in' => $tokenData['expires_in'] ?? null,
                'expires_at' => $expiresAt,
                'scope' => $tokenData['scope'] ?? null,
                'aliexpress_user_id' => $tokenData['aliexpress_user_id'] ?? null,
                'aliexpress_login_id' => $tokenData['aliexpress_login_id'] ?? null,
            ]
        );
    }
}
