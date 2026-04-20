<?php

namespace App\Http\Controllers\Lecteur;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Catalogue : grille de cartes avec recherche et filtres.
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

        if ($request->boolean('available_only')) {
            $query->available();
        }

        $books = $query->orderBy('title')->paginate(12)->withQueryString();

        $categories = [
            'Roman', 'Sciences', 'Histoire', 'Informatique',
            'Philosophie', 'Art', 'Langues', 'Autre',
        ];

        return view('lecteur.books.index', [
            'books'      => $books,
            'categories' => $categories,
            'filters'    => $request->only(['search', 'category', 'available_only']),
        ]);
    }

    /**
     * Fiche détaillée d'un livre.
     */
    public function show(Book $book): View
    {
        $book->loadCount([
            'loans as active_loans_count' => fn($q) => $q->whereNull('returned_at'),
        ]);

        return view('lecteur.books.show', compact('book'));
    }
}
