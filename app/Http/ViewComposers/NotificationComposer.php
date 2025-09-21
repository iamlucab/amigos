<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Notification;

class NotificationComposer
{
    public function compose(View $view)
    {
        $unreadCount = 0;
        
        if (auth()->check() && auth()->user()->role === 'Admin') {
            $unreadCount = Notification::where('user_id', auth()->id())
                ->where('read', false)
                ->count();
        }
        
        // Log the notification count for debugging
        \Log::info('Notification count for user ' . auth()->id() . ': ' . $unreadCount);
        
        $view->with('notificationCount', $unreadCount);
    }
}