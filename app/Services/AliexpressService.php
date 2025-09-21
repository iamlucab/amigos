<?php

namespace App\Services;

use App\Models\AliexpressToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AliexpressService
{
    protected $baseUrl = 'https://api.aliexpress.com';
    protected $clientId;
    protected $clientSecret;
    protected $redirectUrl;

    public function __construct()
    {
        $this->clientId = config('services.aliexpress.client_id');
        $this->clientSecret = config('services.aliexpress.client_secret');
        $this->redirectUrl = config('services.aliexpress.redirect');
    }

    /**
     * Get the current user's AliExpress token
     */
    public function getUserToken()
    {
        $userId = Auth::check() ? Auth::id() : null;
        
        return AliexpressToken::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Refresh the access token
     */
    public function refreshToken($refreshToken)
    {
        $response = Http::asForm()->post('https://oauth.aliexpress.com/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
            'redirect_uri' => $this->redirectUrl,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to refresh token: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Make an API request to AliExpress
     */
    public function makeRequest($method, $endpoint, $params = [], $useAuth = true)
    {
        $token = $this->getUserToken();
        
        if ($useAuth && (!$token || $token->isExpired())) {
            if ($token && $token->refresh_token) {
                // Try to refresh the token
                $refreshResponse = $this->refreshToken($token->refresh_token);
                $this->storeToken($refreshResponse);
                $token = $this->getUserToken();
            } else {
                throw new \Exception('No valid access token available');
            }
        }

        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Content-Type' => 'application/json',
        ];
        
        if ($useAuth && $token) {
            $headers['Authorization'] = 'Bearer ' . $token->access_token;
        }

        $response = Http::withHeaders($headers)->$method($url, $params);

        if ($response->failed()) {
            throw new \Exception('API request failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Store token in database
     */
    protected function storeToken($tokenData)
    {
        $userId = Auth::check() ? Auth::id() : null;
        
        $expiresAt = null;
        if (isset($tokenData['expires_in'])) {
            $expiresAt = now()->addSeconds($tokenData['expires_in']);
        }
        
        AliexpressToken::updateOrCreate(
            ['user_id' => $userId],
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

    /**
     * Get user information
     */
    public function getUserInfo()
    {
        return $this->makeRequest('get', '/api/user/info');
    }

    /**
     * Get user orders
     */
    public function getUserOrders($params = [])
    {
        return $this->makeRequest('get', '/api/user/orders', $params);
    }
}