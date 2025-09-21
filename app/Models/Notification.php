<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'read',
        'notifiable_type',
        'notifiable_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public function scopeForAdmin($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('role', 'Admin');
        });
    }

    public function markAsRead()
    {
        $this->update(['read' => true]);
    }

    public static function createForAdmin($type, $message, $notifiable = null)
    {
        // Get all admin users
        $admins = User::where('role', 'Admin')->get();
        
        foreach ($admins as $admin) {
            $notification = self::create([
                'user_id' => $admin->id,
                'type' => $type,
                'message' => $message,
                'read' => false,
                'notifiable_type' => $notifiable ? get_class($notifiable) : null,
                'notifiable_id' => $notifiable ? $notifiable->id : null
            ]);
        }
        
        return true;
    }
}
