<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Redirige ou affiche le dashboard en fonction du rôle de l'utilisateur.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin'         => redirect()->route('admin.dashboard'),
            'bibliothecaire' => redirect()->route('biblio.dashboard'),
            default         => redirect()->route('lecteur.dashboard'),
        };
    }

    /**
     * Dashboard Administrateur.
     */
    public function admin(): View
    {
        $stats = [
            'users'        => User::count(),
            'books'        => Book::count(),
            'active_loans' => Loan::whereNull('returned_at')->count(),
            'overdue'      => Loan::overdue()->count(),
        ];

        $recentUsers  = User::latest()->limit(5)->get();
        $overdueLoans = Loan::overdue()->with(['book', 'user'])->latest('due_at')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'overdueLoans'));
    }

    /**
     * Dashboard Bibliothécaire.
     */
    public function bibliothecaire(): View
    {
        $stats = [
            'books'        => Book::count(),
            'active_loans' => Loan::whereNull('returned_at')->count(),
            'overdue'      => Loan::overdue()->count(),
            'pending_reservations' => Reservation::pending()->count(),
        ];

        $overdueLoans    = Loan::overdue()->with(['book', 'user'])->latest('due_at')->limit(8)->get();
        $recentLoans     = Loan::with(['book', 'user'])->latest()->limit(5)->get();

        return view('biblio.dashboard', compact('stats', 'overdueLoans', 'recentLoans'));
    }

    /**
     * Dashboard Lecteur.
     */
    public function lecteur(Request $request): View
    {
        $user = $request->user();

        $activeLoans = Loan::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->with('book')
            ->latest()
            ->get();

        $reservations = Reservation::where('user_id', $user->id)
            ->with('book')
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'active_loans'  => $activeLoans->count(),
            'reservations'  => Reservation::where('user_id', $user->id)->where('status', 'en_attente')->count(),
            'overdue'       => $activeLoans->filter(fn($l) => $l->isOverdue())->count(),
            'total_borrowed' => Loan::where('user_id', $user->id)->count(),
        ];

        $latestBooks = Book::latest()->limit(4)->get();

        return view('lecteur.dashboard', compact('stats', 'activeLoans', 'reservations', 'latestBooks'));
    }
}
