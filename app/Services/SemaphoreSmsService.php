<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Member;
use Exception;

class SemaphoreSmsService
{
    protected $apiKey;
    protected $senderName;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name');
        $this->baseUrl = config('services.semaphore.base_url');
    }

    /**
     * Send SMS to a single number
     */
    public function sendSms($mobileNumber, $message)
    {
        if (!$this->apiKey) {
            Log::error('Semaphore API key not configured');
            return false;
        }

        try {
            $response = Http::post($this->baseUrl . '/messages', [
                'apikey' => $this->apiKey,
                'number' => $this->formatMobileNumber($mobileNumber),
                'message' => $message,
                'sendername' => $this->senderName
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('SMS sent successfully', [
                    'number' => $mobileNumber,
                    'message_id' => $data[0]['message_id'] ?? null,
                    'status' => $data[0]['status'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $data[0]['message_id'] ?? null,
                    'status' => $data[0]['status'] ?? null,
                    'response' => $data
                ];
            } else {
                Log::error('SMS sending failed', [
                    'number' => $mobileNumber,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $response->body(),
                    'status_code' => $response->status()
                ];
            }
        } catch (Exception $e) {
            Log::error('SMS sending exception', [
                'number' => $mobileNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS to multiple numbers (bulk)
     */
    public function sendBulkSms($mobileNumbers, $message)
    {
        if (!$this->apiKey) {
            Log::error('Semaphore API key not configured');
            return false;
        }

        $results = [];
        $chunks = array_chunk($mobileNumbers, 100); // Semaphore limit per request

        foreach ($chunks as $chunk) {
            try {
                $numbers = array_map([$this, 'formatMobileNumber'], $chunk);
                
                $response = Http::post($this->baseUrl . '/messages', [
                    'apikey' => $this->apiKey,
                    'number' => implode(',', $numbers),
                    'message' => $message,
                    'sendername' => $this->senderName
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $results = array_merge($results, $data);
                    
                    Log::info('Bulk SMS sent successfully', [
                        'count' => count($chunk),
                        'response_count' => count($data)
                    ]);
                } else {
                    Log::error('Bulk SMS sending failed', [
                        'count' => count($chunk),
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]);
                }
            } catch (Exception $e) {
                Log::error('Bulk SMS sending exception', [
                    'count' => count($chunk),
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Send SMS to all users
     */
    public function sendToAllUsers($message)
    {
        $users = User::whereNotNull('mobile_number')
            ->where('status', 'Active')
            ->get();

        if ($users->isEmpty()) {
            Log::info('No users found for SMS broadcast');
            return false;
        }

        $mobileNumbers = $users->pluck('mobile_number')->toArray();
        return $this->sendBulkSms($mobileNumbers, $message);
    }

    /**
     * Send SMS to all members
     */
    public function sendToAllMembers($message)
    {
        $members = Member::whereNotNull('mobile_number')
            ->where('status', 'Active')
            ->get();

        if ($members->isEmpty()) {
            Log::info('No members found for SMS broadcast');
            return false;
        }

        $mobileNumbers = $members->pluck('mobile_number')->toArray();
        return $this->sendBulkSms($mobileNumbers, $message);
    }

    /**
     * Send SMS to specific users
     */
    public function sendToUsers($userIds, $message)
    {
        $users = User::whereIn('id', $userIds)
            ->whereNotNull('mobile_number')
            ->where('status', 'Active')
            ->get();

        if ($users->isEmpty()) {
            Log::info('No users found for targeted SMS', ['user_ids' => $userIds]);
            return false;
        }

        $mobileNumbers = $users->pluck('mobile_number')->toArray();
        return $this->sendBulkSms($mobileNumbers, $message);
    }

    /**
     * Send SMS to users by role
     */
    public function sendToUsersByRole($role, $message)
    {
        $users = User::where('role', $role)
            ->whereNotNull('mobile_number')
            ->where('status', 'Active')
            ->get();

        if ($users->isEmpty()) {
            Log::info('No users found for role-based SMS', ['role' => $role]);
            return false;
        }

        $mobileNumbers = $users->pluck('mobile_number')->toArray();
        return $this->sendBulkSms($mobileNumbers, $message);
    }

    /**
     * Get account balance
     */
    public function getBalance()
    {
        if (!$this->apiKey) {
            return false;
        }

        try {
            $response = Http::get($this->baseUrl . '/account', [
                'apikey' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'balance' => $data['credit_balance'] ?? 0,
                    'account_name' => $data['account_name'] ?? 'Unknown'
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Error getting SMS balance', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get message status
     */
    public function getMessageStatus($messageId)
    {
        if (!$this->apiKey) {
            return false;
        }

        try {
            $response = Http::get($this->baseUrl . '/messages/' . $messageId, [
                'apikey' => $this->apiKey
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Error getting message status', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format mobile number for Philippines
     */
    protected function formatMobileNumber($mobileNumber)
    {
        // Remove any non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $mobileNumber);
        
        // Handle different formats
        if (strlen($number) == 11 && substr($number, 0, 2) == '09') {
            // 09XXXXXXXXX format - convert to +639XXXXXXXXX
            return '+63' . substr($number, 1);
        } elseif (strlen($number) == 10 && substr($number, 0, 1) == '9') {
            // 9XXXXXXXXX format - convert to +639XXXXXXXXX
            return '+63' . $number;
        } elseif (strlen($number) == 12 && substr($number, 0, 2) == '63') {
            // 639XXXXXXXXX format - convert to +639XXXXXXXXX
            return '+' . $number;
        } elseif (strlen($number) == 13 && substr($number, 0, 3) == '+63') {
            // Already in correct format
            return $number;
        }
        
        // Default: assume it's a 9XXXXXXXXX format
        return '+63' . ltrim($number, '0');
    }

    /**
     * Validate mobile number format
     */
    public function isValidMobileNumber($mobileNumber)
    {
        $formatted = $this->formatMobileNumber($mobileNumber);
        return preg_match('/^\+639[0-9]{9}$/', $formatted);
    }

    /**
     * Calculate message cost (approximate)
     */
    public function calculateCost($message, $recipientCount = 1)
    {
        $messageLength = strlen($message);
        $smsCount = ceil($messageLength / 160); // Standard SMS length
        $costPerSms = 2.50; // Approximate cost in PHP
        
        return $smsCount * $recipientCount * $costPerSms;
    }

    /**
     * Get message statistics
     */
    public function getMessageStats($startDate = null, $endDate = null)
    {
        if (!$this->apiKey) {
            return false;
        }

        try {
            $params = ['apikey' => $this->apiKey];
            
            if ($startDate) {
                $params['startDate'] = $startDate;
            }
            if ($endDate) {
                $params['endDate'] = $endDate;
            }

            $response = Http::get($this->baseUrl . '/messages', $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Error getting message stats', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}