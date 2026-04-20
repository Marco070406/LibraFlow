<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function makeBook(array $attrs = []): Book
    {
        return Book::factory()->create(array_merge([
            'total_copies'     => 2,
            'available_copies' => 0, // Indisponible par défaut pour tester la réservation
        ], $attrs));
    }

    private function makeUser(): User
    {
        return User::factory()->create(['role' => 'lecteur']);
    }

    private function makeReservation(Book $book, User $user): Reservation
    {
        return Reservation::create([
            'book_id'     => $book->id,
            'user_id'     => $user->id,
            'reserved_at' => now(),
            'status'      => 'en_attente',
        ]);
    }

    // ─── Tests ─────────────────────────────────────────────────────────────────

    /**
     * On ne peut pas réserver un livre disponible.
     * La logique métier doit bloquer et rediriger avec un warning.
     */
    public function test_cannot_reserve_available_book(): void
    {
        $user = $this->makeUser();
        $book = $this->makeBook(['available_copies' => 2]); // Disponible

        $this->actingAs($user)
             ->post(route('lecteur.reservations.store', $book))
             ->assertRedirect(route('lecteur.books.show', $book))
             ->assertSessionHas('warning');

        $this->assertDatabaseMissing('reservations', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Un lecteur ne peut pas réserver deux fois le même livre.
     */
    public function test_cannot_reserve_same_book_twice(): void
    {
        $user = $this->makeUser();
        $book = $this->makeBook(); // available_copies = 0

        // Première réservation
        $this->actingAs($user)
             ->post(route('lecteur.reservations.store', $book))
             ->assertRedirect(route('lecteur.reservations.index'))
             ->assertSessionHas('success');

        // Deuxième tentative : doit rediriger vers la liste des réservations
        $this->actingAs($user)
             ->post(route('lecteur.reservations.store', $book))
             ->assertRedirect(route('lecteur.reservations.index'))
             ->assertSessionHas('info');

        // Seulement une réservation en base
        $this->assertDatabaseCount('reservations', 1);
    }

    /**
     * La file est FIFO : le lecteur qui réserve en premier est en position 1.
     */
    public function test_reservation_queue_is_fifo(): void
    {
        $book   = $this->makeBook();
        $first  = $this->makeUser();
        $second = $this->makeUser();
        $third  = $this->makeUser();

        // Créer les réservations dans l'ordre
        $r1 = $this->makeReservation($book, $first);
        sleep(0); // Même seconde possible en test, on injecte des offsets manuels
        $r2 = tap($this->makeReservation($book, $second), fn($r) => $r->update(['reserved_at' => now()->addSecond()]));
        $r3 = tap($this->makeReservation($book, $third), fn($r) => $r->update(['reserved_at' => now()->addSeconds(2)]));

        // La file FIFO est triée par reserved_at ascending
        $queue = Reservation::where('book_id', $book->id)
            ->where('status', 'en_attente')
            ->orderBy('reserved_at')
            ->pluck('user_id')
            ->toArray();

        $this->assertEquals([$first->id, $second->id, $third->id], $queue);
    }

    /**
     * La position dans la file est correctement calculée.
     */
    public function test_queue_position_is_correctly_calculated(): void
    {
        $book  = $this->makeBook();
        $user1 = $this->makeUser();
        $user2 = $this->makeUser();

        $r1 = $this->makeReservation($book, $user1);
        $r1->update(['reserved_at' => now()->subMinute()]);

        $r2 = $this->makeReservation($book, $user2);

        // Position de user2 = 2 (user1 est avant)
        $positionUser2 = Reservation::where('book_id', $book->id)
            ->where('status', 'en_attente')
            ->where('reserved_at', '<=', $r2->reserved_at)
            ->count();

        $this->assertEquals(2, $positionUser2);
    }

    /**
     * Le scope pending() ne retourne que les réservations en_attente.
     */
    public function test_scope_pending_filters_correctly(): void
    {
        $book = $this->makeBook();
        $u1   = $this->makeUser();
        $u2   = $this->makeUser();

        $pending   = $this->makeReservation($book, $u1);
        $cancelled = tap($this->makeReservation($book, $u2), fn($r) => $r->update(['status' => 'annule']));

        $pendingIds = Reservation::pending()->pluck('id');

        $this->assertContains($pending->id, $pendingIds);
        $this->assertNotContains($cancelled->id, $pendingIds);
    }

    /**
     * Annuler une réservation met son statut à 'annule'.
     */
    public function test_cancel_reservation_sets_status_to_annule(): void
    {
        $user        = $this->makeUser();
        $book        = $this->makeBook();
        $reservation = $this->makeReservation($book, $user);

        $this->actingAs($user)
             ->delete(route('lecteur.reservations.destroy', $reservation))
             ->assertRedirect(route('lecteur.reservations.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 'annule',
        ]);
    }
}
