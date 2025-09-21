<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebasePushNotificationService;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{
    protected $pushService;

    public function __construct(FirebasePushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Register device token for push notifications
     */
    public function registerToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'required|in:mobile,web',
            'platform' => 'nullable|in:ios,android,web',
            'device_id' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $tokenData = $request->only([
                'device_token', 'device_type', 'platform', 'device_id', 'app_version'
            ]);

            $deviceToken = $this->pushService->registerDeviceToken($userId, $tokenData);

            if ($deviceToken) {
                return response()->json([
                    'success' => true,
                    'message' => 'Device token registered successfully',
                    'data' => [
                        'id' => $deviceToken->id,
                        'platform' => $deviceToken->platform,
                        'device_type' => $deviceToken->device_type
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to register device token'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while registering device token',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Unregister device token
     */
    public function unregisterToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->pushService->unregisterDeviceToken($request->device_token);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Device token unregistered successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Device token not found or already inactive'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while unregistering device token',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send test notification
     */
    public function sendTestNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->pushService->sendTestNotification($request->device_token);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending test notification',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send notification to specific user (Admin only)
     */
    public function sendToUser(Request $request)
    {
        $this->authorize('admin-only');

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->pushService->sendToUser(
                $request->user_id,
                $request->title,
                $request->body,
                $request->data ?? []
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully to user'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification or no active devices found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending notification',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send broadcast notification to all users (Admin only)
     */
    public function sendBroadcast(Request $request)
    {
        $this->authorize('admin-only');

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'data' => 'nullable|array',
            'platform' => 'nullable|in:ios,android,web',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->platform) {
                $result = $this->pushService->sendToPlatform(
                    $request->platform,
                    $request->title,
                    $request->body,
                    $request->data ?? []
                );
            } else {
                $result = $this->pushService->sendToAllUsers(
                    $request->title,
                    $request->body,
                    $request->data ?? []
                );
            }

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Broadcast notification sent successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send broadcast notification or no active devices found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending broadcast notification',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user's device tokens
     */
    public function getUserTokens()
    {
        try {
            $userId = Auth::id();
            $deviceTokens = DeviceToken::where('user_id', $userId)
                ->active()
                ->orderBy('last_used_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $deviceTokens->map(function ($token) {
                    return [
                        'id' => $token->id,
                        'platform' => $token->platform,
                        'platform_name' => $token->platform_name,
                        'platform_icon' => $token->platform_icon,
                        'device_type' => $token->device_type,
                        'app_version' => $token->app_version,
                        'last_used_at' => $token->last_used_at,
                        'created_at' => $token->created_at,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching device tokens',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove specific device token
     */
    public function removeToken($tokenId)
    {
        try {
            $userId = Auth::id();
            $deviceToken = DeviceToken::where('id', $tokenId)
                ->where('user_id', $userId)
                ->first();

            if (!$deviceToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device token not found'
                ], 404);
            }

            $deviceToken->deactivate();

            return response()->json([
                'success' => true,
                'message' => 'Device token removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing device token',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Clean up expired tokens (Admin only)
     */
    public function cleanupExpiredTokens()
    {
        $this->authorize('admin-only');

        try {
            $deletedCount = $this->pushService->cleanupExpiredTokens();

            return response()->json([
                'success' => true,
                'message' => 'Expired tokens cleaned up successfully',
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while cleaning up expired tokens',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}