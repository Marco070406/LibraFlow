<?php

namespace App\Observers;

use App\Models\Book;
use InvalidArgumentException;

class BookObserver
{
    /**
     * Handle the Book "created" event.
     */
    public function created(Book $book): void
    {
        // Aucune action à la création
    }

    /**
     * Handle the Book "updated" event.
     *
     * Valide que available_copies reste dans les bornes [0, total_copies].
     */
    public function updated(Book $book): void
    {
        if ($book->isDirty('available_copies') || $book->isDirty('total_copies')) {
            if ($book->available_copies < 0) {
                throw new InvalidArgumentException(
                    "Le nombre d'exemplaires disponibles ne peut pas être négatif. Valeur reçue : {$book->available_copies}"
                );
            }

            if ($book->available_copies > $book->total_copies) {
                throw new InvalidArgumentException(
                    "Le nombre d'exemplaires disponibles ({$book->available_copies}) ne peut pas dépasser le nombre total ({$book->total_copies})."
                );
            }
        }
    }
}
