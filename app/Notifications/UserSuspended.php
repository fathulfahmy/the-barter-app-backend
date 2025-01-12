<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserSuspended extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly User $user)
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $title = '';
        $status = '';
        $datetime = null;

        if ($this->user->is_suspended_temporarily) {
            $title = 'Temporary';
            $status = 'temporarily';
            $datetime = 'Suspension ends at: '.$this->user->suspension_ends_at;
        } elseif ($this->user->is_suspended_permanently) {
            $title = 'Permanent';
            $status = 'permanently';
            $datetime = null;
        }

        return (new MailMessage)
            ->subject($title.' Account Suspension - The Barter App')
            ->greeting('Dear '.$this->user->name.',')
            ->line('Your account has been '.$status.' suspended.')
            ->line('Reason: '.$this->user->suspension_reason->name)
            ->line($datetime)
            ->line('Please contact us if you need further assistance.')
            ->line('We thank you for your understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
