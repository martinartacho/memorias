<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriberMagicLink extends Mailable
{
    use Queueable, SerializesModels;

    public string $accessUrl;
    public string $unsubscribeUrl;
    public ?string $narracionTitulo;

    public function __construct(
        public Subscriber $subscriber,
        ?string $redirectSlug = null,
        ?string $narracionTitulo = null
    ) {
        $params = ['token' => $subscriber->token];
        if ($redirectSlug) {
            $params['redirect'] = $redirectSlug;
        }

        $this->accessUrl      = route('suscripcion.acceder', $params);
        $this->unsubscribeUrl = route('suscripcion.baja', ['token' => $subscriber->token]);
        $this->narracionTitulo = $narracionTitulo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu acceso a Memorias sin orden',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscriber-magic-link',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
