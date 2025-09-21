<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class FirebasePushNotificationService
{
    protected $messaging;
    protected $factory;

    public function __construct()
    {
        try {
            $credentialsPath = config('services.firebase.credentials');
            
            if (!file_exists($credentialsPath)) {
                Log::warning('Firebase credentials file not found: ' . $credentialsPath);
                return;
            }

            $this->factory = (new Factory)
                ->withServiceAccount($credentialsPath);
                
            if (config('services.firebase.database_url')) {
                $this->factory = $this->factory->withDatabaseUri(config('services.firebase.database_url'));
            }
            
            $this->messaging = $this->factory->createMessaging();
        } catch (Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a single device token
     */
    public function sendToToken($token, $title, $body, $data = [], $options = [])
    {
        if (!$this->messaging) {
            Log::error('Firebase messaging not initialized');
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);

            // Add custom data
            if (!empty($data)) {
                $message = $message->withData($data);
            }

            // Add platform-specific configurations
            $message = $this->addPlatformConfigs($message, $options);

            $result = $this->messaging->send($message);
            
            Log::info('Push notification sent successfully', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'result' => $result
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to send push notification', [
                'token' => substr($token, 0, 20) . '...',
                'error' => $e->getMessage()
            ]);
            
            // If token is invalid, deactivate it
            if (strpos($e->getMessage(), 'registration-token-not-registered') !== false) {
                $this->deactivateToken($token);
            }
            
            return false;
        }
    }

    /**
     * Send notification to multiple device tokens
     */
    public function sendToMultipleTokens($tokens, $title, $body, $data = [], $options = [])
    {
        if (!$this->messaging || empty($tokens)) {
            return false;
        }

        $results = [];
        $chunks = array_chunk($tokens, 500); // FCM limit is 500 tokens per request

        foreach ($chunks as $chunk) {
            try {
                $notification = Notification::create($title, $body);
                
                $message = CloudMessage::new()
                    ->withNotification($notification);

                // Add custom data
                if (!empty($data)) {
                    $message = $message->withData($data);
                }

                // Add platform-specific configurations
                $message = $this->addPlatformConfigs($message, $options);

                $result = $this->messaging->sendMulticast($message, $chunk);
                $results[] = $result;

                Log::info('Multicast push notification sent', [
                    'tokens_count' => count($chunk),
                    'success_count' => $result->successes()->count(),
                    'failure_count' => $result->failures()->count()
                ]);

                // Handle failed tokens
                foreach ($result->failures() as $failure) {
                    $failedToken = $failure->target()->value();
                    Log::warning('Failed to send to token: ' . substr($failedToken, 0, 20) . '...', [
                        'error' => $failure->error()->getMessage()
                    ]);
                    
                    // Deactivate invalid tokens
                    if (strpos($failure->error()->getMessage(), 'registration-token-not-registered') !== false) {
                        $this->deactivateToken($failedToken);
                    }
                }

            } catch (Exception $e) {
                Log::error('Failed to send multicast notification', [
                    'error' => $e->getMessage(),
                    'tokens_count' => count($chunk)
                ]);
            }
        }

        return $results;
    }

    /**
     * Send notification to a user (all their active devices)
     */
    public function sendToUser($userId, $title, $body, $data = [], $options = [])
    {
        $deviceTokens = DeviceToken::getActiveTokensForUser($userId);
        
        if ($deviceTokens->isEmpty()) {
            Log::info('No active device tokens found for user: ' . $userId);
            return false;
        }

        $tokens = $deviceTokens->pluck('device_token')->toArray();
        return $this->sendToMultipleTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers($userIds, $title, $body, $data = [], $options = [])
    {
        $deviceTokens = DeviceToken::whereIn('user_id', $userIds)
            ->active()
            ->get();
            
        if ($deviceTokens->isEmpty()) {
            Log::info('No active device tokens found for users', ['user_ids' => $userIds]);
            return false;
        }

        $tokens = $deviceTokens->pluck('device_token')->toArray();
        return $this->sendToMultipleTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * Send notification to all users
     */
    public function sendToAllUsers($title, $body, $data = [], $options = [])
    {
        $deviceTokens = DeviceToken::active()->get();
        
        if ($deviceTokens->isEmpty()) {
            Log::info('No active device tokens found');
            return false;
        }

        $tokens = $deviceTokens->pluck('device_token')->toArray();
        return $this->sendToMultipleTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * Send notification based on platform
     */
    public function sendToPlatform($platform, $title, $body, $data = [], $options = [])
    {
        $deviceTokens = DeviceToken::active()
            ->platform($platform)
            ->get();
            
        if ($deviceTokens->isEmpty()) {
            Log::info('No active device tokens found for platform: ' . $platform);
            return false;
        }

        $tokens = $deviceTokens->pluck('device_token')->toArray();
        return $this->sendToMultipleTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * Add platform-specific configurations
     */
    protected function addPlatformConfigs($message, $options)
    {
        // Android configuration
        if (isset($options['android'])) {
            $androidConfig = AndroidConfig::fromArray($options['android']);
            $message = $message->withAndroidConfig($androidConfig);
        }

        // iOS configuration
        if (isset($options['apns'])) {
            $apnsConfig = ApnsConfig::fromArray($options['apns']);
            $message = $message->withApnsConfig($apnsConfig);
        }

        // Web push configuration
        if (isset($options['webpush'])) {
            $webPushConfig = WebPushConfig::fromArray($options['webpush']);
            $message = $message->withWebPushConfig($webPushConfig);
        }

        return $message;
    }

    /**
     * Deactivate invalid token
     */
    protected function deactivateToken($token)
    {
        DeviceToken::where('device_token', $token)->update(['is_active' => false]);
        Log::info('Deactivated invalid device token: ' . substr($token, 0, 20) . '...');
    }

    /**
     * Register device token
     */
    public function registerDeviceToken($userId, $tokenData)
    {
        try {
            $deviceToken = DeviceToken::createOrUpdate($userId, $tokenData);
            
            Log::info('Device token registered', [
                'user_id' => $userId,
                'platform' => $tokenData['platform'] ?? 'unknown',
                'device_type' => $tokenData['device_type'] ?? 'mobile'
            ]);

            return $deviceToken;
        } catch (Exception $e) {
            Log::error('Failed to register device token', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Unregister device token
     */
    public function unregisterDeviceToken($token)
    {
        try {
            $deviceToken = DeviceToken::where('device_token', $token)->first();
            
            if ($deviceToken) {
                $deviceToken->deactivate();
                Log::info('Device token unregistered: ' . substr($token, 0, 20) . '...');
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Failed to unregister device token', [
                'token' => substr($token, 0, 20) . '...',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clean up expired tokens
     */
    public function cleanupExpiredTokens()
    {
        try {
            $deletedCount = DeviceToken::cleanupExpired();
            Log::info('Cleaned up expired device tokens', ['count' => $deletedCount]);
            return $deletedCount;
        } catch (Exception $e) {
            Log::error('Failed to cleanup expired tokens', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Test notification
     */
    public function sendTestNotification($token)
    {
        if (!$this->messaging) {
            Log::error('Firebase messaging service not initialized for test notification');
            return false;
        }

        Log::info('Attempting to send test notification', [
            'token_preview' => substr($token, 0, 20) . '...',
            'firebase_initialized' => $this->messaging !== null
        ]);

        $result = $this->sendToToken(
            $token,
            'Test Notification',
            'This is a test notification from E-Bili Admin Panel',
            ['type' => 'test', 'timestamp' => now()->toISOString()]
        );

        Log::info('Test notification result', [
            'success' => $result !== false,
            'result' => $result
        ]);

        return $result;
    }
}