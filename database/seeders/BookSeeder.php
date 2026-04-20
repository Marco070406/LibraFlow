<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Seed the books table with 20 sample books.
     */
    public function run(): void
    {
        $books = [
            // --- Roman ---
            [
                'title' => 'L\'Aventure ambiguë',
                'author' => 'Cheikh Hamidou Kane',
                'isbn' => '978-2-264-03397-1',
                'category' => 'Roman',
                'description' => 'Roman philosophique sur le choc des cultures entre l\'Afrique et l\'Occident.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'Une si longue lettre',
                'author' => 'Mariama Bâ',
                'isbn' => '978-2-7233-0109-0',
                'category' => 'Roman',
                'description' => 'Roman épistolaire sur la condition féminine au Sénégal.',
                'total_copies' => 2,
                'available_copies' => 2,
            ],
            [
                'title' => 'Le Petit Prince',
                'author' => 'Antoine de Saint-Exupéry',
                'isbn' => '978-2-07-061275-8',
                'category' => 'Roman',
                'description' => 'Conte poétique et philosophique publié en 1943.',
                'total_copies' => 4,
                'available_copies' => 4,
            ],
            [
                'title' => 'Les Bouts de bois de Dieu',
                'author' => 'Ousmane Sembène',
                'isbn' => '978-2-266-16896-7',
                'category' => 'Roman',
                'description' => 'Roman inspiré de la grève des cheminots de 1947-1948 au Sénégal.',
                'total_copies' => 2,
                'available_copies' => 2,
            ],

            // --- Sciences ---
            [
                'title' => 'Cosmos',
                'author' => 'Carl Sagan',
                'isbn' => '978-0-345-53943-4',
                'category' => 'Sciences',
                'description' => 'Exploration de l\'univers, de la science et de la civilisation humaine.',
                'total_copies' => 1,
                'available_copies' => 1,
            ],
            [
                'title' => 'Une brève histoire du temps',
                'author' => 'Stephen Hawking',
                'isbn' => '978-2-08-128138-2',
                'category' => 'Sciences',
                'description' => 'Introduction à la cosmologie pour le grand public.',
                'total_copies' => 2,
                'available_copies' => 2,
            ],
            [
                'title' => 'L\'Origine des espèces',
                'author' => 'Charles Darwin',
                'isbn' => '978-2-08-070685-5',
                'category' => 'Sciences',
                'description' => 'Ouvrage fondateur de la théorie de l\'évolution par sélection naturelle.',
                'total_copies' => 1,
                'available_copies' => 1,
            ],
            [
                'title' => 'Physique quantique pour les nuls',
                'author' => 'Blandine Pluchet',
                'isbn' => '978-2-7540-5893-1',
                'category' => 'Sciences',
                'description' => 'Introduction accessible à la mécanique quantique.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],

            // --- Histoire ---
            [
                'title' => 'Nations nègres et culture',
                'author' => 'Cheikh Anta Diop',
                'isbn' => '978-2-7087-0521-8',
                'category' => 'Histoire',
                'description' => 'Étude majeure sur l\'histoire et la civilisation africaines.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'L\'Afrique noire précoloniale',
                'author' => 'Cheikh Anta Diop',
                'isbn' => '978-2-7087-0532-4',
                'category' => 'Histoire',
                'description' => 'Étude comparative des systèmes politiques et sociaux de l\'Afrique précoloniale.',
                'total_copies' => 2,
                'available_copies' => 2,
            ],
            [
                'title' => 'Sapiens : Une brève histoire de l\'humanité',
                'author' => 'Yuval Noah Harari',
                'isbn' => '978-2-226-25710-7',
                'category' => 'Histoire',
                'description' => 'Panorama de l\'histoire de l\'humanité depuis les premiers Homo sapiens.',
                'total_copies' => 2,
                'available_copies' => 2,
            ],
            [
                'title' => 'Sundiata : An Epic of Old Mali',
                'author' => 'Djibril Tamsir Niane',
                'isbn' => '978-1-4058-4942-0',
                'category' => 'Histoire',
                'description' => 'L\'épopée de Soundjata Keïta, fondateur de l\'Empire du Mali.',
                'total_copies' => 1,
                'available_copies' => 1,
            ],

            // --- Informatique ---
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '978-0-13-235088-4',
                'category' => 'Informatique',
                'description' => 'Guide des bonnes pratiques pour écrire du code propre et maintenable.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'Design Patterns',
                'author' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                'isbn' => '978-0-201-63361-0',
                'category' => 'Informatique',
                'description' => 'Catalogue des patrons de conception orientés objet (Gang of Four).',
                'total_copies' => 2,
                'available_copies' => 2,
            ],
            [
                'title' => 'Introduction to Algorithms',
                'author' => 'Thomas H. Cormen',
                'isbn' => '978-0-262-03384-8',
                'category' => 'Informatique',
                'description' => 'Référence académique en algorithmique et structures de données.',
                'total_copies' => 4,
                'available_copies' => 4,
            ],
            [
                'title' => 'Laravel: Up & Running',
                'author' => 'Matt Stauffer',
                'isbn' => '978-1-098-15326-7',
                'category' => 'Informatique',
                'description' => 'Guide complet du framework Laravel pour les développeurs PHP.',
                'total_copies' => 2,
                'available_copies' => 2,
            ],

            // --- Philosophie ---
            [
                'title' => 'Discours sur le colonialisme',
                'author' => 'Aimé Césaire',
                'isbn' => '978-2-7087-0191-3',
                'category' => 'Philosophie',
                'description' => 'Essai sur les mécanismes et les conséquences du colonialisme.',
                'total_copies' => 2,
                'available_copies' => 2,
            ],
            [
                'title' => 'Les Damnés de la terre',
                'author' => 'Frantz Fanon',
                'isbn' => '978-2-7071-0030-1',
                'category' => 'Philosophie',
                'description' => 'Analyse de la décolonisation et de la psychopathologie de la colonisation.',
                'total_copies' => 1,
                'available_copies' => 1,
            ],
            [
                'title' => 'Méditations métaphysiques',
                'author' => 'René Descartes',
                'isbn' => '978-2-08-070707-4',
                'category' => 'Philosophie',
                'description' => 'Ouvrage fondamental de la philosophie moderne sur la connaissance et l\'existence.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'L\'Éthique',
                'author' => 'Baruch Spinoza',
                'isbn' => '978-2-07-032256-0',
                'category' => 'Philosophie',
                'description' => 'Traité philosophique majeur sur Dieu, la nature et la liberté humaine.',
                'total_copies' => 1,
                'available_copies' => 1,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
