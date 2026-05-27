<?php

namespace App\Mail;

use App\Models\Narracion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NarracionPublicada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Narracion $narracion) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva narración: «' . $this->narracion->titulo . '»',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.narracion-publicada',
        );
    }
}
