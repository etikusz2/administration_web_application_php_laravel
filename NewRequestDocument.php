<?php

namespace App\Notifications;

use App\Models\ServiceDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class NewRequestDocument extends Notification
{
    use Queueable;

    protected $document;

    /**
     * Create a new notification instance.
     *
     * @param  ServiceDocument  $document
     * @return void
     */
    public function __construct(ServiceDocument $document)
    {
        $this->document = $document;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'A fost primită un document nou pe serviciul ' . $this->document->service->serviceName,
            'document_id' => $this->document->id,
        ];
    }
}
