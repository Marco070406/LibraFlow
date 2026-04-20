<?php

namespace App\Helpers;

use App\Models\Book;

class BookHelper
{
    /**
     * Retourne le statut de disponibilité d'un livre.
     *
     * @return array{label: string, color: string, icon: string, can_borrow: bool, can_reserve: bool}
     */
    public static function getAvailabilityStatus(Book $book): array
    {
        if ($book->available_copies > 0) {
            return [
                'label'      => "Disponible ({$book->available_copies} ex.)",
                'color'      => 'emerald',
                'icon'       => 'check-circle',
                'can_borrow' => true,
                'can_reserve' => false,
            ];
        }

        // Tous empruntés
        return [
            'label'      => 'Tous empruntés',
            'color'      => 'amber',
            'icon'       => 'clock',
            'can_borrow' => false,
            'can_reserve' => true,
        ];
    }
}
