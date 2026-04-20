<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\PenaltyPayment;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tableau de bord administrateur avec KPIs et graphiques CSS.
     */
    public function dashboard(): View
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        // ── KPIs ──────────────────────────────────────────────────────────────
        $stats = [
            'total_books'          => Book::sum('total_copies'),
            'distinct_titles'      => Book::count(),
            'active_loans'         => Loan::whereNull('returned_at')->count(),
            'overdue_loans'        => Loan::overdue()->count(),
            'returned_this_month'  => Loan::whereNotNull('returned_at')
                                         ->where('returned_at', '>=', $startOfMonth)
                                         ->count(),
            'pending_reservations' => Reservation::pending()->count(),
            'total_users'          => User::count(),
        ];

        // ── Top 5 livres les plus empruntés ──────────────────────────────────
        $topBooks = Loan::selectRaw('book_id, COUNT(*) as loan_count')
            ->with('book')
            ->groupBy('book_id')
            ->orderByDesc('loan_count')
            ->limit(5)
            ->get();

        $maxLoans = $topBooks->max('loan_count') ?: 1;

        // ── Top 3 catégories populaires ───────────────────────────────────────
        $topCategories = Loan::join('books', 'loans.book_id', '=', 'books.id')
            ->selectRaw('books.category, COUNT(*) as loan_count')
            ->groupBy('books.category')
            ->orderByDesc('loan_count')
            ->limit(3)
            ->get();

        // ── 5 derniers emprunts en retard ─────────────────────────────────────
        $overdueLoans = Loan::overdue()
            ->with(['book', 'user'])
            ->oldest('due_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'topBooks', 'maxLoans', 'topCategories', 'overdueLoans'
        ));
    }

    /**
     * Formulaire des paramètres globaux.
     */
    public function settings(): View
    {
        $loanDays    = Setting::get('loan_duration_days', config('libraflow.loan_duration_days', 14));
        $dailyPenalty = Setting::get('daily_penalty', config('libraflow.daily_penalty', 100));

        return view('admin.settings', compact('loanDays', 'dailyPenalty'));
    }

    /**
     * Mise à jour des paramètres globaux.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'loan_duration_days' => ['required', 'integer', 'min:1', 'max:60'],
            'daily_penalty'      => ['required', 'numeric', 'min:0'],
        ]);

        Setting::set('loan_duration_days', $request->integer('loan_duration_days'));
        Setting::set('daily_penalty', $request->input('daily_penalty'));

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Les paramètres ont été mis à jour avec succès.');
    }

    /**
     * Liste des utilisateurs avec rôle, emprunts actifs et pénalités.
     */
    public function users(): View
    {
        $users = User::withCount([
                'loans as active_loans_count' => fn ($q) => $q->whereNull('returned_at'),
            ])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Changer le rôle d'un utilisateur (impossible sur soi-même).
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $request->validate([
            'role' => ['required', 'in:admin,bibliothecaire,lecteur'],
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Le rôle de {$user->name} a été mis à jour : {$request->role}.");
    }

    /**
     * Liste des lecteurs avec des pénalités non payées.
     */
    public function penalties(): View
    {
        // Emprunts retournés en retard avec pénalité, non entièrement payés
        $overdueLoans = Loan::with(['book', 'user', 'penaltyPayments'])
            ->whereNotNull('returned_at')
            ->whereNotNull('penalty_amount')
            ->where('penalty_amount', '>', 0)
            ->get();

        // Grouper par utilisateur et calculer le solde restant
        $grouped = $overdueLoans->groupBy('user_id')->map(function ($loans) {
            $total   = $loans->sum('penalty_amount');
            $paid    = $loans->flatMap->penaltyPayments->sum('amount_paid');
            $balance = max(0, $total - $paid);

            return [
                'user'    => $loans->first()->user,
                'loans'   => $loans,
                'total'   => $total,
                'paid'    => $paid,
                'balance' => $balance,
            ];
        })->filter(fn ($row) => $row['balance'] > 0)->values();

        return view('admin.penalties.index', compact('grouped'));
    }

    /**
     * Marquer une pénalité comme payée.
     */
    public function markPenaltyPaid(Request $request, Loan $loan): RedirectResponse
    {
        $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        PenaltyPayment::create([
            'loan_id'    => $loan->id,
            'user_id'    => $loan->user_id,
            'amount_paid' => $request->input('amount_paid'),
            'paid_at'    => now(),
            'notes'      => $request->input('notes'),
        ]);

        return back()->with('success', "Paiement de {$request->input('amount_paid')} DA enregistré pour « {$loan->book->title} ».");
    }
}
