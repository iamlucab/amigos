<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebasePushNotificationService;
use App\Services\SemaphoreSmsService;
use App\Models\DeviceToken;
use App\Models\SmsLog;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    protected $pushService;
    protected $smsService;

    public function __construct(FirebasePushNotificationService $pushService, SemaphoreSmsService $smsService)
    {
        $this->middleware('can:admin-only');
        $this->pushService = $pushService;
        $this->smsService = $smsService;
    }

    /**
     * Show notification management dashboard
     */
    public function index()
    {
        // Get statistics
        $pushStats = $this->getPushNotificationStats();
        $smsStats = SmsLog::getStatistics('month');
        $deviceStats = $this->getDeviceStats();
        $recentSmsLogs = SmsLog::getRecent(5);
        
        // Get SMS balance
        $smsBalance = $this->smsService->getBalance();
        
        // Get user notifications
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.notifications.index', compact(
            'pushStats',
            'smsStats',
            'deviceStats',
            'recentSmsLogs',
            'smsBalance',
            'notifications'
        ));
    }

    /**
     * Show push notification form
     */
    public function pushNotifications()
    {
        $deviceStats = $this->getDeviceStats();
        $users = User::where('status', 'Active')->get(['id', 'name', 'email']);
        
        return view('admin.notifications.push', compact('deviceStats', 'users'));
    }

    /**
     * Show SMS blasting form
     */
    public function smsBlasting()
    {
        $userStats = $this->getUserStats();
        $users = User::whereNotNull('mobile_number')
            ->where('status', 'Active')
            ->get(['id', 'name', 'mobile_number']);
        $smsBalance = $this->smsService->getBalance();
        
        return view('admin.notifications.sms', compact('userStats', 'users', 'smsBalance'));
    }

    /**
     * Send push notification
     */
    public function sendPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'target_type' => 'required|in:all,platform,users',
            'platform' => 'nullable|in:ios,android,web',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $result = false;
            $recipientCount = 0;

            switch ($request->target_type) {
                case 'all':
                    $result = $this->pushService->sendToAllUsers(
                        $request->title,
                        $request->body,
                        $request->data ?? []
                    );
                    $recipientCount = DeviceToken::active()->count();
                    break;

                case 'platform':
                    $result = $this->pushService->sendToPlatform(
                        $request->platform,
                        $request->title,
                        $request->body,
                        $request->data ?? []
                    );
                    $recipientCount = DeviceToken::active()->platform($request->platform)->count();
                    break;

                case 'users':
                    $result = $this->pushService->sendToUsers(
                        $request->user_ids,
                        $request->title,
                        $request->body,
                        $request->data ?? []
                    );
                    $recipientCount = DeviceToken::whereIn('user_id', $request->user_ids)->active()->count();
                    break;
            }

            if ($result) {
                return back()->with('success', "Push notification sent successfully to {$recipientCount} devices!");
            } else {
                return back()->with('error', 'Failed to send push notification or no active devices found.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error sending push notification: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS blast
     */
    public function sendSmsBlast(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'target_type' => 'required|in:all_users,all_members,role_based,specific_users',
            'role' => 'nullable|in:Admin,Staff,Member',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'campaign_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Determine recipients
            $recipients = [];
            $recipientType = $request->target_type;

            switch ($request->target_type) {
                case 'all_users':
                    $users = User::whereNotNull('mobile_number')->where('status', 'Active')->get();
                    $recipients = $users->pluck('mobile_number')->toArray();
                    break;

                case 'all_members':
                    $members = Member::whereNotNull('mobile_number')->where('status', 'Active')->get();
                    $recipients = $members->pluck('mobile_number')->toArray();
                    break;

                case 'role_based':
                    $users = User::where('role', $request->role)
                        ->whereNotNull('mobile_number')
                        ->where('status', 'Active')
                        ->get();
                    $recipients = $users->pluck('mobile_number')->toArray();
                    break;

                case 'specific_users':
                    $users = User::whereIn('id', $request->user_ids)
                        ->whereNotNull('mobile_number')
                        ->where('status', 'Active')
                        ->get();
                    $recipients = $users->pluck('mobile_number')->toArray();
                    $recipientType = 'bulk';
                    break;
            }

            if (empty($recipients)) {
                return back()->with('error', 'No recipients found for the selected criteria.');
            }

            // Calculate estimated cost
            $estimatedCost = $this->smsService->calculateCost($request->message, count($recipients));

            // Create SMS log
            $smsLog = SmsLog::createLog([
                'recipient_type' => $recipientType,
                'recipients' => $recipients,
                'message' => $request->message,
                'estimated_cost' => $estimatedCost,
                'campaign_name' => $request->campaign_name,
                'notes' => $request->notes,
            ]);

            // Send SMS
            $smsLog->markAsSending();
            $result = $this->smsService->sendBulkSms($recipients, $request->message);

            if ($result && !empty($result)) {
                // Process results
                $successful = 0;
                $failed = 0;
                $messageIds = [];

                foreach ($result as $response) {
                    if (isset($response['status']) && $response['status'] === 'Queued') {
                        $successful++;
                        if (isset($response['message_id'])) {
                            $messageIds[] = $response['message_id'];
                        }
                    } else {
                        $failed++;
                    }
                }

                $smsLog->updateResponse($result, $messageIds);
                $smsLog->markAsCompleted($successful, $failed);

                DB::commit();

                return back()->with('success', "SMS blast sent successfully! {$successful} messages queued, {$failed} failed. Estimated cost: ₱" . number_format($estimatedCost, 2));
            } else {
                $smsLog->markAsFailed('No response from SMS service');
                DB::rollback();
                return back()->with('error', 'Failed to send SMS blast. Please check your SMS service configuration.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error sending SMS blast: ' . $e->getMessage());
        }
    }

    /**
     * Show SMS history
     */
    public function smsHistory()
    {
        $smsLogs = SmsLog::with('sentBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $rawStats = SmsLog::getStatistics('month');
        
        // Map the stats to match what the view expects
        $stats = [
            'total_campaigns' => $rawStats['total_campaigns'],
            'total_sent' => $rawStats['successful_sends'],
            'pending' => $rawStats['pending_campaigns'],
            'total_cost' => $rawStats['total_cost'],
        ];

        return view('admin.notifications.sms-history', compact('smsLogs', 'stats'));
    }

    /**
     * Show device tokens
     */
    public function deviceTokens()
    {
        $deviceTokens = DeviceToken::with('user')
            ->active()
            ->orderBy('last_used_at', 'desc')
            ->paginate(50);

        $stats = [
            'total_devices' => DeviceToken::count(),
            'active_devices' => DeviceToken::active()->count(),
            'android_devices' => DeviceToken::active()->platform('android')->count(),
            'ios_devices' => DeviceToken::active()->platform('ios')->count(),
        ];

        return view('admin.notifications.device-tokens', compact('deviceTokens', 'stats'));
    }

    /**
     * Test push notification
     */
    public function testPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid device token provided.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if Firebase is properly configured
            if (!config('services.firebase.credentials') || !file_exists(config('services.firebase.credentials'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase credentials not configured properly. Please check your Firebase setup.'
                ]);
            }

            // Log the test attempt
            \Log::info('Testing push notification', [
                'token_preview' => substr($request->device_token, 0, 20) . '...',
                'user_id' => auth()->id()
            ]);

            $result = $this->pushService->sendTestNotification($request->device_token);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully!',
                    'result' => is_string($result) ? $result : 'Notification queued'
                ]);
            } else {
                // Check if the token exists in our database
                $tokenExists = \App\Models\DeviceToken::where('token', $request->device_token)->exists();
                
                $message = 'Failed to send test notification. ';
                if (!$tokenExists) {
                    $message .= 'The device token is not registered in our system. ';
                }
                $message .= 'The token may be invalid, expired, or the device is not reachable.';
                
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Push notification test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => substr($request->device_token, 0, 20) . '...'
            ]);
            
            // Provide more specific error messages
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'registration-token-not-registered') !== false) {
                $errorMessage = 'The device token is not registered or has expired. Please re-register the device.';
            } elseif (strpos($errorMessage, 'invalid-registration-token') !== false) {
                $errorMessage = 'The device token format is invalid. Please check the token.';
            } elseif (strpos($errorMessage, 'authentication') !== false) {
                $errorMessage = 'Firebase authentication failed. Please check your Firebase credentials.';
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending test notification: ' . $errorMessage
            ]);
        }
    }

    /**
     * Test SMS
     */
    public function testSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->smsService->sendSms($request->mobile_number, $request->message);

            if ($result['success']) {
                return response()->json(['success' => true, 'message' => 'Test SMS sent successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to send test SMS: ' . $result['error']]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get SMS balance
     */
    public function getSmsBalance()
    {
        try {
            $balance = $this->smsService->getBalance();
            return response()->json($balance);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Cleanup expired device tokens
     */
    public function cleanupTokens()
    {
        try {
            $deletedCount = $this->pushService->cleanupExpiredTokens();
            return back()->with('success', "Cleaned up {$deletedCount} expired device tokens.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error cleaning up tokens: ' . $e->getMessage());
        }
    }

    /**
     * Get push notification statistics
     */
    private function getPushNotificationStats()
    {
        return [
            'total_devices' => DeviceToken::count(),
            'active_devices' => DeviceToken::active()->count(),
            'ios_devices' => DeviceToken::active()->platform('ios')->count(),
            'android_devices' => DeviceToken::active()->platform('android')->count(),
            'web_devices' => DeviceToken::active()->platform('web')->count(),
        ];
    }

    /**
     * Get device statistics
     */
    private function getDeviceStats()
    {
        return [
            'total' => DeviceToken::count(),
            'active' => DeviceToken::active()->count(),
            'ios' => DeviceToken::active()->platform('ios')->count(),
            'android' => DeviceToken::active()->platform('android')->count(),
            'web' => DeviceToken::active()->platform('web')->count(),
            'mobile' => DeviceToken::active()->deviceType('mobile')->count(),
        ];
    }

    /**
     * Get user statistics for SMS
     */
    private function getUserStats()
    {
        return [
            'total_users' => User::where('status', 'Active')->count(),
            'users_with_mobile' => User::whereNotNull('mobile_number')->where('status', 'Active')->count(),
            'total_members' => Member::where('status', 'Active')->count(),
            'members_with_mobile' => Member::whereNotNull('mobile_number')->where('status', 'Active')->count(),
            'admins' => User::where('role', 'Admin')->whereNotNull('mobile_number')->where('status', 'Active')->count(),
            'staff' => User::where('role', 'Staff')->whereNotNull('mobile_number')->where('status', 'Active')->count(),
            'members' => User::where('role', 'Member')->whereNotNull('mobile_number')->where('status', 'Active')->count(),
        ];
    }

    /**
     * Retry failed SMS
     */
    public function retrySms($id)
    {
        try {
            $smsLog = SmsLog::findOrFail($id);
            
            if ($smsLog->status !== 'failed' && $smsLog->failed_count == 0) {
                return response()->json(['success' => false, 'message' => 'No failed messages to retry.']);
            }

            // Get failed recipients (this would need to be implemented based on your SMS service response)
            $failedRecipients = $smsLog->recipients; // Simplified - you might need to track which specific numbers failed
            
            if (empty($failedRecipients)) {
                return response()->json(['success' => false, 'message' => 'No recipients to retry.']);
            }

            // Create new SMS log for retry
            $retryLog = SmsLog::createLog([
                'recipient_type' => 'bulk',
                'recipients' => $failedRecipients,
                'message' => $smsLog->message,
                'estimated_cost' => $this->smsService->calculateCost($smsLog->message, count($failedRecipients)),
                'campaign_name' => ($smsLog->campaign_name ?? 'SMS Campaign #' . $smsLog->id) . ' (Retry)',
                'notes' => 'Retry of failed messages from campaign #' . $smsLog->id,
            ]);

            // Send SMS
            $retryLog->markAsSending();
            $result = $this->smsService->sendBulkSms($failedRecipients, $smsLog->message);

            if ($result && !empty($result)) {
                $successful = 0;
                $failed = 0;
                $messageIds = [];

                foreach ($result as $response) {
                    if (isset($response['status']) && $response['status'] === 'Queued') {
                        $successful++;
                        if (isset($response['message_id'])) {
                            $messageIds[] = $response['message_id'];
                        }
                    } else {
                        $failed++;
                    }
                }

                $retryLog->updateResponse($result, $messageIds);
                $retryLog->markAsCompleted($successful, $failed);

                return response()->json([
                    'success' => true,
                    'message' => "Retry initiated successfully! {$successful} messages queued, {$failed} failed."
                ]);
            } else {
                $retryLog->markAsFailed('No response from SMS service');
                return response()->json(['success' => false, 'message' => 'Failed to retry SMS. Please check your SMS service configuration.']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrying SMS: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete SMS log
     */
    public function deleteSmsLog($id)
    {
        try {
            $smsLog = SmsLog::findOrFail($id);
            $smsLog->delete();

            return response()->json(['success' => true, 'message' => 'SMS campaign log deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting SMS log: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = \App\Models\Notification::where('user_id', auth()->id())->findOrFail($id);
            $notification->markAsRead();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error marking notification as read: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            \App\Models\Notification::where('user_id', auth()->id())->unread()->update(['read' => true]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error marking all notifications as read: ' . $e->getMessage()]);
        }
    }
}