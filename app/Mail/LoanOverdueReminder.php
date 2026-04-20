<?php

namespace App\Mail;

use App\Models\Loan;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanOverdueReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Nombre de jours de retard.
     */
    public int $daysOverdue;

    /**
     * Montant de la pénalité en FCFA.
     */
    public float $penaltyAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(public Loan $loan)
    {
        $this->daysOverdue = (int) $loan->due_at->diffInDays(now());

        $dailyPenalty = (float) Setting::get(
            'daily_penalty',
            config('libraflow.daily_penalty', 100)
        );

        $this->penaltyAmount = $this->daysOverdue * $dailyPenalty;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Rappel de retour : {$this->loan->book->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-overdue',
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
