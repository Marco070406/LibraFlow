<?php

namespace App\Http\Controllers\Biblio;

use App\Http\Controllers\Controller;
use App\Jobs\SendBookAvailableNotification;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService) {}

    /**
     * Liste tous les emprunts avec filtre de scope (actifs / en retard / retournés).
     * Pagination 20 éléments par page.
     */
    public function index(Request $request): View
    {
        $scope = $request->query('scope', 'actifs');

        $query = Loan::with(['book', 'user'])->latest('borrowed_at');

        $query = match ($scope) {
            'retard'    => $query->overdue(),
            'retournes' => $query->whereNotNull('returned_at'),
            default     => $query->whereNull('returned_at'),   // actifs
        };

        $loans = $query->paginate(20)->withQueryString();

        // Nombre d'emprunts en retard pour l'indicateur d'alerte
        $overdueCount = Loan::overdue()->count();

        return view('biblio.loans.index', compact('loans', 'scope', 'overdueCount'));
    }

    /**
     * Formulaire de création d'un emprunt manuel.
     * Pré-remplit due_at = now() + loan_duration_days (via LoanService).
     */
    public function create(): View
    {
        $dueDate = $this->loanService->calculateDueDate(now());
        $dueDateFormatted = $this->loanService->formatDueDate($dueDate);

        return view('biblio.loans.create', compact('dueDate', 'dueDateFormatted'));
    }

    /**
     * Enregistre un nouvel emprunt.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'book_id' => ['required', 'exists:books,id'],
        ]);

        $book = Book::findOrFail($request->book_id);
        $user = User::findOrFail($request->user_id);

        // Vérifier la disponibilité
        if ($book->available_copies <= 0) {
            return back()
                ->withInput()
                ->with('error', "Le livre « {$book->title} » n'a plus d'exemplaire disponible.");
        }

        // Vérifier que l'utilisateur n'a pas déjà ce livre emprunté
        $alreadyBorrowed = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($alreadyBorrowed) {
            return back()
                ->withInput()
                ->with('error', "{$user->name} a déjà ce livre en cours d'emprunt et ne peut pas l'emprunter à nouveau.");
        }

        $borrowedAt = now();
        $dueAt = $this->loanService->calculateDueDate($borrowedAt);

        // Créer l'emprunt
        Loan::create([
            'user_id'    => $user->id,
            'book_id'    => $book->id,
            'borrowed_at' => $borrowedAt,
            'due_at'     => $dueAt,
        ]);

        // Décrémenter le stock via update direct (l'Observer valide les bornes)
        $book->decrement('available_copies');

        $dueDateFormatted = $this->loanService->formatDueDate($dueAt);

        return redirect()
            ->route('biblio.loans.index')
            ->with('success', "Emprunt enregistré pour {$user->name}. Retour attendu le {$dueDateFormatted}.");
    }

    /**
     * Enregistrer le retour d'un livre.
     */
    public function returnBook(Loan $loan): RedirectResponse
    {
        // Ne pas retourner deux fois
        if ($loan->returned_at !== null) {
            return back()->with('warning', 'Ce livre a déjà été retourné.');
        }

        $returnedAt = now();
        $penalty = $this->loanService->calculatePenalty($loan);

        // Mettre à jour l'emprunt
        $loan->update([
            'returned_at'    => $returnedAt,
            'penalty_amount' => $penalty > 0 ? $penalty : null,
        ]);

        // Réincrémenter le stock
        $loan->book->increment('available_copies');

        // Vérifier s'il existe une réservation en attente pour ce livre
        $reservation = Reservation::where('book_id', $loan->book_id)
            ->pending()
            ->oldest('reserved_at')
            ->first();

        if ($reservation) {
            $reservation->notify();
            SendBookAvailableNotification::dispatch($reservation);
        }

        // Construire le message récapitulatif
        if ($penalty > 0) {
            $daysOverdue = (int) $loan->due_at->diffInDays($returnedAt);
            $message = "Retour enregistré pour « {$loan->book->title} ». Retard de {$daysOverdue} jour(s) — pénalité : {$penalty} DA.";
        } else {
            $message = "Retour enregistré pour « {$loan->book->title} ». Merci !";
        }

        return redirect()
            ->route('biblio.loans.index')
            ->with('success', $message);
    }

    /**
     * Liste dédiée aux emprunts en retard avec calcul temps/pénalité.
     */
    public function overdue(): View
    {
        $loans = Loan::with(['book', 'user'])
            ->overdue()
            ->oldest('due_at')
            ->paginate(20);

        $dailyPenalty = (float) \App\Models\Setting::get('daily_penalty', config('libraflow.daily_penalty', 100));

        return view('biblio.loans.overdue', compact('loans', 'dailyPenalty'));
    }
}
