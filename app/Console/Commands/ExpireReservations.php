<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireReservations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reservations:expire';

    /**
     * The console command description.
     */
    protected $description = 'Expire les réservations notifiées depuis plus de 3 jours sans emprunt.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredCount = Reservation::where('status', 'notifie')
            ->where('notified_at', '<=', Carbon::now()->subDays(3))
            ->update(['status' => 'annule']);

        $message = "ExpireReservations : {$expiredCount} réservation(s) expirée(s) passée(s) à 'annule'.";

        Log::info($message);
        $this->info($message);

        return Command::SUCCESS;
    }
}
