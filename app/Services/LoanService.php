<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Setting;
use Carbon\Carbon;

class LoanService
{
    /**
     * Calcule la date de retour attendue à partir de la date d'emprunt.
     * Utilise Setting::get('loan_duration_days') avec fallback sur 14 jours.
     */
    public function calculateDueDate(Carbon $borrowedAt): Carbon
    {
        $days = (int) Setting::get('loan_duration_days', 14);

        return $borrowedAt->copy()->addDays($days);
    }

    /**
     * Calcule la pénalité de retard pour un emprunt.
     * Si returned_at est renseigné, utilise returned_at ; sinon now().
     * Retourne 0.0 si l'emprunt n'est pas en retard.
     */
    public function calculatePenalty(Loan $loan): float
    {
        // Si le livre a été rendu → plus de pénalité en cours (elle est figée en base)
        if (!is_null($loan->returned_at)) {
            return 0.0;
        }

        // Si la date de retour n'est pas encore dépassée → 0
        if (!$loan->due_at->isPast()) {
            return 0.0;
        }

        // Emprunt en cours ET en retard : calcul des jours depuis due_at
        $daysOverdue = (int) $loan->due_at->diffInDays(now());

        $dailyPenalty = (float) Setting::get('daily_penalty', config('libraflow.daily_penalty', 100));

        return round($daysOverdue * $dailyPenalty, 2);
    }

    /**
     * Formate une date Carbon en français long.
     * Exemple : "lundi 14 juillet 2025"
     */
    public function formatDueDate(Carbon $date): string
    {
        return $date->locale('fr')->translatedFormat('l d F Y');
    }
}
