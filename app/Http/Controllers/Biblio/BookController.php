<?php

namespace App\Http\Controllers\Biblio;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Catégories autorisées.
     */
    private const CATEGORIES = [
        'Roman', 'Sciences', 'Histoire', 'Informatique',
        'Philosophie', 'Art', 'Langues', 'Autre',
    ];

    /**
     * Liste paginée avec filtres (catégorie, recherche).
     */
    public function index(Request $request): View
    {
        $query = Book::query();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $books = $query->orderBy('title')->paginate(15)->withQueryString();

        return view('biblio.books.index', [
            'books'      => $books,
            'categories' => self::CATEGORIES,
            'filters'    => $request->only(['search', 'category']),
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        return view('biblio.books.create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    /**
     * Enregistrement d'un nouveau livre.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        // Upload couverture
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        // available_copies = total_copies à la création
        $validated['available_copies'] = $validated['total_copies'];

        Book::create($validated);

        return redirect()->route('biblio.books.index')
            ->with('success', 'Livre ajouté au catalogue avec succès.');
    }

    /**
     * Fiche livre complète.
     */
    public function show(Book $book): View
    {
        $book->load([
            'loans' => fn($q) => $q->whereNull('returned_at')->with('user'),
            'reservations' => fn($q) => $q->where('status', 'en_attente')->with('user'),
        ]);

        return view('biblio.books.show', compact('book'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Book $book): View
    {
        return view('biblio.books.edit', [
            'book'       => $book,
            'categories' => self::CATEGORIES,
        ]);
    }

    /**
     * Mise à jour du livre.
     */
    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate($this->validationRules($book->id));

        // Upload couverture
        if ($request->hasFile('cover_image')) {
            // Supprimer l'ancienne
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        // Recalcul available_copies si total_copies est modifié
        if (isset($validated['total_copies']) && $validated['total_copies'] !== $book->total_copies) {
            $activeLoans = Loan::where('book_id', $book->id)
                ->whereNull('returned_at')
                ->count();
            $validated['available_copies'] = max(0, $validated['total_copies'] - $activeLoans);
        }

        $book->update($validated);

        return redirect()->route('biblio.books.index')
            ->with('success', "Le livre « {$book->title} » a été mis à jour.");
    }

    /**
     * Suppression (uniquement si aucun emprunt actif).
     */
    public function destroy(Book $book): RedirectResponse
    {
        $activeLoans = Loan::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->count();

        if ($activeLoans > 0) {
            return back()->with('error',
                "Impossible de supprimer ce livre : {$activeLoans} emprunt(s) actif(s).");
        }

        // Supprimer la couverture
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('biblio.books.index')
            ->with('success', "Le livre « {$book->title} » a été supprimé.");
    }

    /**
     * Règles de validation communes.
     */
    private function validationRules(?int $bookId = null): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'author'       => ['required', 'string', 'max:255'],
            'isbn'         => ['nullable', 'string', Rule::unique('books', 'isbn')->ignore($bookId)],
            'category'     => ['required', 'string', Rule::in(self::CATEGORIES)],
            'description'  => ['nullable', 'string'],
            'total_copies' => ['required', 'integer', 'min:1', 'max:99'],
            'cover_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
