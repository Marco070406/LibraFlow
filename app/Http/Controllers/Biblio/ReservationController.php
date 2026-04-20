<?php

namespace App\Http\Controllers\Biblio;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReservationController extends Controller
{
    /**
     * Toutes les réservations en_attente groupées par livre avec position dans file.
     */
    public function index(): View
    {
        // Récupère toutes les réservations actives (en_attente + notifie),
        // ordonnées par livre puis par date (FIFO)
        $reservations = Reservation::with(['book', 'user'])
            ->whereIn('status', ['en_attente', 'notifie'])
            ->orderBy('book_id')
            ->orderBy('reserved_at')
            ->get()
            ->groupBy('book_id');

        return view('biblio.reservations.index', compact('reservations'));
    }

    /**
     * Annuler une réservation depuis l'interface bibliothécaire.
     */
    public function cancel(Reservation $reservation): RedirectResponse
    {
        $reservation->update(['status' => 'annule']);

        return redirect()
            ->route('biblio.reservations.index')
            ->with('success', "La réservation de {$reservation->user->name} pour « {$reservation->book->title} » a été annulée.");
    }
}
