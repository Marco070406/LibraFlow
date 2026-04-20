<?php

namespace App\Jobs;

use App\Mail\LoanOverdueReminder;
use App\Models\Loan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOverdueReminders implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Récupère tous les emprunts en retard dont le rappel n'a pas encore été envoyé aujourd'hui
        $loans = Loan::with(['book', 'user'])
            ->overdue()
            ->where(function ($query) {
                $query->whereNull('reminder_sent_at')
                      ->orWhereDate('reminder_sent_at', '<', today());
            })
            ->get();

        $sentCount = 0;

        foreach ($loans as $loan) {
            $user = $loan->user;

            if (!$user || !$user->email) {
                Log::warning("SendOverdueReminders : pas d'email pour l'emprunt #{$loan->id}");
                continue;
            }

            Mail::to($user->email, $user->name)
                ->send(new LoanOverdueReminder($loan));

            // Marquer le rappel comme envoyé aujourd'hui
            $loan->update(['reminder_sent_at' => now()]);

            $sentCount++;

            Log::info("Rappel retard envoyé à {$user->email} pour « {$loan->book->title} » ({$loan->due_at->diffInDays(now())} jours de retard).");
        }

        Log::info("SendOverdueReminders terminé : {$sentCount} rappel(s) envoyé(s).");
    }
}
