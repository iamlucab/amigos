<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MemberApprovedNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail']; // Can add 'database' if you want in-app alert
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Membership Has Been Approved!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Congratulations! Your membership has been approved and activated.')
            ->action('Login Now', url('/login'))
            ->line('Thank you for being part of Hugpong Amigos.');
    }


    public function toArray($notifiable)
    {
        return [
            'message' => 'Your membership has been approved.',
            'action_url' => url('/login'),
        ];
    }
}