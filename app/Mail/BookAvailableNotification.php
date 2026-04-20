<?php

namespace App\Mail;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookAvailableNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * La date limite pour emprunter le livre (3 jours après la notification).
     */
    public Carbon $deadline;

    /**
     * Create a new message instance.
     */
    public function __construct(public Reservation $reservation)
    {
        $this->deadline = Carbon::parse($reservation->notified_at ?? now())->addDays(3);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📚 Votre livre est disponible : {$this->reservation->book->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.book-available',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
