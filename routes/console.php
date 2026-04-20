<?php

use App\Console\Commands\ExpireReservations;
use App\Console\Commands\SendLoanReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tâches planifiées
|--------------------------------------------------------------------------
*/

// Expire quotidiennement les réservations notifiées depuis plus de 3 jours
Schedule::command(ExpireReservations::class)->daily();

// Envoie chaque matin les rappels préventifs (J-2) et les rappels de retard
Schedule::command(SendLoanReminders::class)->dailyAt('08:00');
