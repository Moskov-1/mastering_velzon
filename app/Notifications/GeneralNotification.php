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

   
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }
    
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message
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
