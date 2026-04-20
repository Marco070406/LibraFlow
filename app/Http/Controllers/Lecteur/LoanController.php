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
}
