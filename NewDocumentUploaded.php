<?php

namespace App\Notifications;

use App\Models\ServiceDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDocumentUploaded extends Notification
{
    use Queueable;

    protected $document;

    public function __construct(ServiceDocument $document)
    {
        $this->document = $document;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Un nou document a fost încărcat: ' . $this->document->documentName,
            'upload_document_id' => $this->document->id,
            'notifiable_type' => "App\Models\User"
        ];
    }
}
