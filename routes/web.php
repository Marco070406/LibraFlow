<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Biblio\BookController as BiblioBookController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Biblio\LoanController as BiblioLoanController;
use App\Http\Controllers\Biblio\ReservationController as BiblioReservationController;
use App\Http\Controllers\Lecteur\BookController as LecteurBookController;
use App\Http\Controllers\Lecteur\LoanController as LecteurLoanController;
use App\Http\Controllers\Lecteur\ReservationController as LecteurReservationController;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard — Redirection selon le rôle
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Routes du profil utilisateur (Breeze)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Routes Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Paramètres globaux
    Route::get('/settings',  [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    // Gestion des utilisateurs
    Route::get('/users',                      [AdminController::class, 'users'])->name('users.index');
    Route::post('/users/{user}/role',         [AdminController::class, 'updateUserRole'])->name('users.role');

    // Pénalités
    Route::get('/penalties',                     [AdminController::class, 'penalties'])->name('penalties.index');
    Route::post('/penalties/{loan}/mark-paid',   [AdminController::class, 'markPenaltyPaid'])->name('penalties.markPaid');
});

/*
|--------------------------------------------------------------------------
| Routes Bibliothécaire
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'bibliothecaire'])->prefix('biblio')->name('biblio.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'bibliothecaire'])->name('dashboard');

    // Gestion du catalogue de livres (CRUD)
    Route::resource('books', BiblioBookController::class);

    // Gestion des emprunts et retours
    Route::get('/loans',              [BiblioLoanController::class, 'index'])  ->name('loans.index');
    Route::get('/loans/create',       [BiblioLoanController::class, 'create']) ->name('loans.create');
    Route::post('/loans',             [BiblioLoanController::class, 'store'])  ->name('loans.store');
    Route::get('/loans/overdue',      [BiblioLoanController::class, 'overdue'])->name('loans.overdue');
    Route::post('/loans/{loan}/return', [BiblioLoanController::class, 'returnBook'])->name('loans.return');

    // Gestion des réservations
    Route::get('/reservations',                         [BiblioReservationController::class, 'index']) ->name('reservations.index');
    Route::post('/reservations/{reservation}/cancel',   [BiblioReservationController::class, 'cancel'])->name('reservations.cancel');

    // Export PDF des retards (via laravel-dompdf)
});

/*
|--------------------------------------------------------------------------
| Routes Lecteur
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'lecteur'])->prefix('lecteur')->name('lecteur.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'lecteur'])->name('dashboard');

    // Consultation du catalogue (lecture seule)
    Route::get('/books', [LecteurBookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [LecteurBookController::class, 'show'])->name('books.show');

    // Historique des emprunts personnels
    Route::get('/loans', [LecteurLoanController::class, 'index'])->name('loans.index');
    Route::post('/loans/{book}', [LecteurLoanController::class, 'store'])->name('loans.store');

    // Réservations
    Route::get('/reservations',                        [LecteurReservationController::class, 'index'])  ->name('reservations.index');
    Route::post('/reservations/{book}',                [LecteurReservationController::class, 'store'])  ->name('reservations.store');
    Route::delete('/reservations/{reservation}',       [LecteurReservationController::class, 'destroy'])->name('reservations.destroy');

    // Consultation des pénalités
    // Demande d'emprunt
});

/*
|--------------------------------------------------------------------------
| Routes API internes — Recherche AJAX (auth requis)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/api/users/search', function (Request $request) {
        $q = $request->query('q', '');
        if (strlen($q) < 2) return response()->json([]);

        return User::where('role', 'lecteur')
            ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('email', 'LIKE', "%{$q}%");
            })
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();
    });

    Route::get('/api/books/search', function (Request $request) {
        $q = $request->query('q', '');
        if (strlen($q) < 2) return response()->json([]);

        return Book::available()
            ->where(function ($query) use ($q) {
                $query->where('title', 'LIKE', "%{$q}%")
                      ->orWhere('author', 'LIKE', "%{$q}%");
            })
            ->select('id', 'title', 'author', 'available_copies')
            ->limit(10)
            ->get();
    });
});

/*
|--------------------------------------------------------------------------
| Routes d'authentification (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
