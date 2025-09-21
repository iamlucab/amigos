<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sent_by',
        'recipient_type',
        'recipients',
        'message',
        'sender_name',
        'total_recipients',
        'successful_sends',
        'failed_sends',
        'estimated_cost',
        'status',
        'semaphore_response',
        'message_ids',
        'campaign_name',
        'notes',
        'sent_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'semaphore_response' => 'array',
        'message_ids' => 'array',
        'estimated_cost' => 'decimal:2',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the user who sent the SMS
     */
    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Scope for completed SMS logs
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed SMS logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for pending SMS logs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for SMS logs by recipient type
     */
    public function scopeByRecipientType($query, $type)
    {
        return $query->where('recipient_type', $type);
    }

    /**
     * Scope for SMS logs sent today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('sent_at', Carbon::today());
    }

    /**
     * Scope for SMS logs sent this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('sent_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope for SMS logs sent this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sent_at', Carbon::now()->month)
                    ->whereYear('sent_at', Carbon::now()->year);
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute()
    {
        if ($this->total_recipients == 0) {
            return 0;
        }
        
        return round(($this->successful_sends / $this->total_recipients) * 100, 2);
    }

    /**
     * Get failure rate percentage
     */
    public function getFailureRateAttribute()
    {
        if ($this->total_recipients == 0) {
            return 0;
        }
        
        return round(($this->failed_sends / $this->total_recipients) * 100, 2);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'completed' => 'badge-success',
            'failed' => 'badge-danger',
            'sending' => 'badge-warning',
            'pending' => 'badge-secondary',
            default => 'badge-light',
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        return match ($this->status) {
            'completed' => 'Completed',
            'failed' => 'Failed',
            'sending' => 'Sending',
            'pending' => 'Pending',
            default => 'Unknown',
        };
    }

    /**
     * Get recipient type display name
     */
    public function getRecipientTypeDisplayAttribute()
    {
        return match ($this->recipient_type) {
            'single' => 'Single User',
            'bulk' => 'Bulk Recipients',
            'all_users' => 'All Users',
            'all_members' => 'All Members',
            'role_based' => 'Role-based',
            default => 'Unknown',
        };
    }

    /**
     * Get formatted message preview
     */
    public function getMessagePreviewAttribute()
    {
        return strlen($this->message) > 100 
            ? substr($this->message, 0, 100) . '...' 
            : $this->message;
    }

    /**
     * Get message character count
     */
    public function getMessageLengthAttribute()
    {
        return strlen($this->message);
    }

    /**
     * Get estimated SMS count
     */
    public function getSmsCountAttribute()
    {
        return ceil($this->message_length / 160);
    }

    /**
     * Get recipient count (alias for total_recipients)
     */
    public function getRecipientCountAttribute()
    {
        return $this->total_recipients;
    }

    /**
     * Get successful count (alias for successful_sends)
     */
    public function getSuccessfulCountAttribute()
    {
        return $this->successful_sends;
    }

    /**
     * Get failed count (alias for failed_sends)
     */
    public function getFailedCountAttribute()
    {
        return $this->failed_sends;
    }

    /**
     * Get cost (alias for estimated_cost)
     */
    public function getCostAttribute()
    {
        return $this->estimated_cost;
    }

    /**
     * Get error message from notes if status is failed
     */
    public function getErrorMessageAttribute()
    {
        if ($this->status === 'failed' && $this->notes) {
            return $this->notes;
        }
        return null;
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted($successfulSends = null, $failedSends = null)
    {
        $this->update([
            'status' => 'completed',
            'successful_sends' => $successfulSends ?? $this->successful_sends,
            'failed_sends' => $failedSends ?? $this->failed_sends,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'notes' => $reason ? "Failed: {$reason}" : $this->notes,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark as sending
     */
    public function markAsSending()
    {
        $this->update([
            'status' => 'sending',
        ]);
    }

    /**
     * Update response data
     */
    public function updateResponse($response, $messageIds = null)
    {
        $this->update([
            'semaphore_response' => $response,
            'message_ids' => $messageIds,
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public static function getStatistics($period = 'month')
    {
        $query = static::query();
        
        switch ($period) {
            case 'today':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
        }
        
        return [
            'total_campaigns' => $query->count(),
            'completed_campaigns' => $query->clone()->completed()->count(),
            'failed_campaigns' => $query->clone()->failed()->count(),
            'pending_campaigns' => $query->clone()->pending()->count(),
            'total_recipients' => $query->sum('total_recipients'),
            'successful_sends' => $query->sum('successful_sends'),
            'failed_sends' => $query->sum('failed_sends'),
            'total_cost' => $query->sum('estimated_cost'),
        ];
    }

    /**
     * Get recent logs
     */
    public static function getRecent($limit = 10)
    {
        return static::with('sentBy')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create log entry
     */
    public static function createLog($data)
    {
        return static::create([
            'sent_by' => auth()->id(),
            'recipient_type' => $data['recipient_type'],
            'recipients' => $data['recipients'],
            'message' => $data['message'],
            'sender_name' => $data['sender_name'] ?? config('services.semaphore.sender_name'),
            'total_recipients' => count($data['recipients']),
            'estimated_cost' => $data['estimated_cost'] ?? 0,
            'campaign_name' => $data['campaign_name'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);
    }
}
