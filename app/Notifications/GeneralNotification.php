<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ?string $type,
        public ?string $message,
    )
    {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    
    public function toArray(object $notifiable): array
    {
        return [
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            
        ];
    }

    public function toBroadcast($notifiable)
    {

        return new BroadcastMessage([
            'data' => [
                'type' => $this->type,
                'message' => $this->message ?? 'A dealing event has been triggered!',
            ]
        ]);
    }
}
