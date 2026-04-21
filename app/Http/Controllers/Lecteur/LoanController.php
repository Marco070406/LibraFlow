<?php

namespace App\Http\Controllers\Lecteur;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService) {}

    /**
     * Historique des emprunts du lecteur connecté.
     */
    public function index(): View
    {
        $loans = Loan::with('book')
            ->where('user_id', auth()->id())
            ->latest('borrowed_at')
            ->paginate(15);

        $loanService = $this->loanService;

        return view('lecteur.loans.index', compact('loans', 'loanService'));
    }

    /**
     * Permet au lecteur d'emprunter un livre direct.
     */
    public function store(\Illuminate\Http\Request $request, \App\Models\Book $book): \Illuminate\Http\RedirectResponse
    {
        // 🛑 Bloquer l'emprunt si le lecteur a des livres en retard
        if (Loan::where('user_id', auth()->id())->overdue()->exists()) {
            return back()->with('error', 'Action bloquée : Vous avez des emprunts en retard. Veuillez restituer vos livres avant d\'effectuer un nouvel emprunt.');
        }

        // Vérifier disponibilité
        if ($book->available_copies <= 0) {
            return back()->with('error', 'Ce livre n\'est plus disponible.');
        }

        // Vérifier que le lecteur ne l'a pas déjà emprunté sans l'avoir rendu
        $alreadyBorrowed = Loan::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($alreadyBorrowed) {
            return back()->with('warning', 'Vous avez déjà emprunté ce livre.');
        }

        $borrowedAt = now();
        $dueAt = $this->loanService->calculateDueDate($borrowedAt);

        Loan::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'borrowed_at' => $borrowedAt,
            'due_at' => $dueAt,
        ]);

        $book->decrement('available_copies');

        return back()->with('success', 'Félicitations, vous avez emprunté ce livre avec succès !');
    }
}
