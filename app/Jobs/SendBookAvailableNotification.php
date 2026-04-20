<?php

namespace App\Jobs;

use App\Mail\BookAvailableNotification;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookAvailableNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Reservation $reservation)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // S'assurer que les relations sont chargées
        $this->reservation->loadMissing(['book', 'user']);

        $user = $this->reservation->user;

        if (!$user || !$user->email) {
            Log::warning("SendBookAvailableNotification : pas d'email pour la réservation #{$this->reservation->id}");
            return;
        }

        Mail::to($user->email, $user->name)
            ->send(new BookAvailableNotification($this->reservation));

        Log::info("Notification 'livre disponible' envoyée à {$user->email} pour le livre « {$this->reservation->book->title} ».");
    }
}
