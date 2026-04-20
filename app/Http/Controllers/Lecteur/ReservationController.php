<?php

namespace App\Http\Controllers\Lecteur;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReservationController extends Controller
{
    /**
     * Réserver un livre (uniquement si available_copies = 0).
     */
    public function store(Book $book): RedirectResponse
    {
        // Si des exemplaires sont disponibles → emprunt direct
        if ($book->available_copies > 0) {
            return redirect()
                ->route('lecteur.books.show', $book)
                ->with('warning', "Le livre « {$book->title} » est disponible. Vous pouvez le demander directement à la bibliothèque.");
        }

        $userId = auth()->id();

        // Vérifier si le lecteur a déjà une réservation active sur ce livre
        $existing = Reservation::where('book_id', $book->id)
            ->where('user_id', $userId)
            ->whereIn('status', ['en_attente', 'notifie'])
            ->first();

        if ($existing) {
            // Calculer sa position actuelle dans la file
            $position = Reservation::where('book_id', $book->id)
                ->where('status', 'en_attente')
                ->where('reserved_at', '<=', $existing->reserved_at)
                ->count();

            return redirect()
                ->route('lecteur.reservations.index')
                ->with('info', "Vous êtes déjà en position {$position} dans la file d'attente pour « {$book->title} ».");
        }

        // Position dans la file (FIFO)
        $position = Reservation::where('book_id', $book->id)
            ->pending()
            ->count() + 1;

        Reservation::create([
            'book_id'     => $book->id,
            'user_id'     => $userId,
            'reserved_at' => now(),
            'status'      => 'en_attente',
        ]);

        return redirect()
            ->route('lecteur.reservations.index')
            ->with('success', "Vous êtes en position {$position} dans la file d'attente pour « {$book->title} ». Nous vous préviendrons dès qu'un exemplaire est disponible.");
    }

    /**
     * Annuler une réservation.
     */
    public function destroy(Reservation $reservation): RedirectResponse
    {
        // Sécurité : la réservation doit appartenir au lecteur connecté
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        $reservation->update(['status' => 'annule']);

        return redirect()
            ->route('lecteur.reservations.index')
            ->with('success', 'Votre réservation a été annulée.');
    }

    /**
     * Historique des réservations du lecteur connecté.
     */
    public function index(): View
    {
        $reservations = Reservation::with('book')
            ->where('user_id', auth()->id())
            ->latest('reserved_at')
            ->paginate(15);

        return view('lecteur.reservations.index', compact('reservations'));
    }
}
