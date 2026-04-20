<?php

namespace App\Console\Commands;

use App\Jobs\SendOverdueReminders;
use App\Mail\LoanDueSoonReminder;
use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLoanReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'loans:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Envoie les rappels préventifs (J-2) et dispatch le job de rappels de retard.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('── Début de loans:send-reminders ──');

        // ── 1. Rappels préventifs : emprunts dont la date de retour = aujourd'hui + 2 jours ──
        $targetDate = now()->addDays(2)->toDateString();

        $dueSoonLoans = Loan::with(['book', 'user'])
            ->whereNull('returned_at')
            ->whereDate('due_at', $targetDate)
            ->get();

        $dueSoonCount = 0;

        foreach ($dueSoonLoans as $loan) {
            $user = $loan->user;

            if (!$user || !$user->email) {
                Log::warning("SendLoanReminders (J-2) : pas d'email pour l'emprunt #{$loan->id}");
                continue;
            }

            Mail::to($user->email, $user->name)
                ->send(new LoanDueSoonReminder($loan));

            $dueSoonCount++;

            Log::info("Rappel J-2 envoyé à {$user->email} pour « {$loan->book->title} » (retour le {$loan->due_at->toDateString()}).");
        }

        $this->info("  ✓ Rappels préventifs (J-2) : {$dueSoonCount} email(s) envoyé(s).");

        // ── 2. Dispatch du job de rappels de retard ──
        SendOverdueReminders::dispatch();

        $this->info('  ✓ Job SendOverdueReminders dispatché vers la queue.');
        $this->info('── Fin de loans:send-reminders ──');

        return Command::SUCCESS;
    }
}
