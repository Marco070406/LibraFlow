<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\PenaltyPayment;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecteurs = User::where('role', 'lecteur')->get();
        if ($lecteurs->isEmpty()) {
            $this->command->warn('Aucun lecteur trouvé pour les scénarios.');
            return;
        }

        $allBooks = Book::all();
        if ($allBooks->count() < 5) {
            $this->command->warn('Pas assez de livres pour générer des scénarios.');
            return;
        }

        // Récupération du tarif journalier
        $dailyPenalty = (float) Setting::get('daily_penalty', 100);

        // ---------------------------------------------------------
        // 1. SCÉNARIO : Emprunts Actifs dans les temps
        // ---------------------------------------------------------
        $lecteur1 = $lecteurs[0]; // Moussa
        $book1 = $allBooks[0];
        
        Loan::create([
            'user_id' => $lecteur1->id,
            'book_id' => $book1->id,
            'borrowed_at' => now()->subDays(5),
            'due_at' => now()->addDays(5), // Reste 5 jours
            'returned_at' => null,
        ]);
        $book1->decrement('available_copies');

        // ---------------------------------------------------------
        // 2. SCÉNARIO : Emprunt En Retard (Non Rendu)
        // ---------------------------------------------------------
        $lecteur2 = $lecteurs[1]; // Fatou
        $book2 = $allBooks[1];

        Loan::create([
            'user_id' => $lecteur2->id,
            'book_id' => $book2->id,
            'borrowed_at' => now()->subDays(20),
            'due_at' => now()->subDays(5), // 5 jours de retard
            'returned_at' => null,
        ]);
        $book2->decrement('available_copies');

        // ---------------------------------------------------------
        // 3. SCÉNARIO : Emprunt Retourné en Retard -> Pénalité Générée
        // ---------------------------------------------------------
        // Et on suppose que c'est soit payé, soit impayé.
        $lecteur3 = $lecteurs[2]; // Ibrahima
        $book3 = $allBooks[2];

        // Retard de 3 jours, pénalité = 3 * daily_penalty
        $returnedLoan = Loan::create([
            'user_id' => $lecteur3->id,
            'book_id' => $book3->id,
            'borrowed_at' => now()->subDays(25),
            'due_at' => now()->subDays(10), // Devait être rendu il y a 10j
            'returned_at' => now()->subDays(7), // Rendu il y a 7j (donc avec 3 jours de retard)
            'penalty_amount' => 3 * $dailyPenalty, 
        ]);
        // Le stock ayant été rendu, on ne décrémente pas les available_copies.

        // Scénario Pénalité PAYÉE partiellement ou totalement (pour tester Admin/Pénalités)
        PenaltyPayment::create([
            'loan_id' => $returnedLoan->id,
            'amount_paid' => $returnedLoan->penalty_amount / 2, // A payé moitié
            'paid_at' => now()->subDays(6),
            'notes' => 'A payé la moitié en espèces.',
        ]);

        // ---------------------------------------------------------
        // 4. SCÉNARIO : Livre Épuisé + Réservations
        // ---------------------------------------------------------
        $book4 = $allBooks[3];
        // On épuise complètement le livre via de faux emprunts (pour justifier la réservation)
        $qtyToBorrow = $book4->available_copies;
        for ($i = 0; $i < $qtyToBorrow; $i++) {
            Loan::create([
                'user_id' => $lecteurs->random()->id,
                'book_id' => $book4->id,
                'borrowed_at' => now()->subDays(3),
                'due_at' => now()->addDays(7),
                'returned_at' => null,
            ]);
            $book4->decrement('available_copies');
        }

        // On crée 2 réservations pour ce livre épuisé
        Reservation::create([
            'user_id' => $lecteur1->id,
            'book_id' => $book4->id,
            'reserved_at' => now()->subDays(2),
            'status' => 'en_attente',
        ]);
        
        Reservation::create([
            'user_id' => $lecteur2->id,
            'book_id' => $book4->id,
            'reserved_at' => now()->subDays(1),
            'status' => 'en_attente',
        ]);
        
        // Réservation Annulée pour tester l'historique
        Reservation::create([
            'user_id' => $lecteur3->id,
            'book_id' => $allBooks[4]->id,
            'reserved_at' => now()->subDays(15),
            'status' => 'annule',
        ]);

        $this->command->info('Scénarios insérés : Emprunts actifs, Retards, Pénalités (historique de paiement) et Réservations (file d\'attente).');
    }
}
