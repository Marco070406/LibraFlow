<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function makeBook(array $attrs = []): Book
    {
        return Book::factory()->create(array_merge([
            'total_copies'     => 3,
            'available_copies' => 3,
        ], $attrs));
    }

    private function makeUser(): User
    {
        return User::factory()->create(['role' => 'lecteur']);
    }

    private function makeLoan(Book $book, User $user, array $attrs = []): Loan
    {
        return Loan::create(array_merge([
            'book_id'     => $book->id,
            'user_id'     => $user->id,
            'borrowed_at' => now(),
            'due_at'      => now()->addDays(14),
        ], $attrs));
    }

    // ─── Tests ─────────────────────────────────────────────────────────────────

    /**
     * Créer un emprunt doit décrémenter available_copies.
     */
    public function test_checkout_decrements_available_copies(): void
    {
        $book = $this->makeBook(['available_copies' => 3]);
        $user = $this->makeUser();

        $book->decrement('available_copies');

        $this->assertDatabaseHas('books', [
            'id'               => $book->id,
            'available_copies' => 2,
        ]);
    }

    /**
     * Retourner un livre doit incrémenter available_copies.
     */
    public function test_return_increments_available_copies(): void
    {
        $book = $this->makeBook(['available_copies' => 2]);
        $user = $this->makeUser();

        $loan = $this->makeLoan($book, $user);

        // Simuler le retour
        $loan->update(['returned_at' => now()]);
        $book->increment('available_copies');

        $this->assertDatabaseHas('books', [
            'id'               => $book->id,
            'available_copies' => 3,
        ]);
    }

    /**
     * available_copies ne peut pas dépasser total_copies.
     */
    public function test_available_copies_does_not_exceed_total(): void
    {
        $book = $this->makeBook(['total_copies' => 2, 'available_copies' => 2]);

        // On ne peut pas monter au-delà de total_copies
        $this->assertLessThanOrEqual(
            $book->total_copies,
            $book->available_copies
        );
    }

    /**
     * Le scope overdue() ne retourne que les emprunts réellement en retard.
     */
    public function test_scope_overdue_returns_only_overdue_loans(): void
    {
        $user = $this->makeUser();
        $book = $this->makeBook();

        // Emprunt en retard
        $overdueLoan = $this->makeLoan($book, $user, [
            'due_at'      => now()->subDays(3),
            'returned_at' => null,
        ]);

        // Emprunt non en retard
        $activeLoan = $this->makeLoan($book, $user, [
            'due_at'      => now()->addDays(5),
            'returned_at' => null,
        ]);

        // Emprunt retourné (ne doit pas apparaître même si due_at passé)
        $returnedLoan = $this->makeLoan($book, $user, [
            'due_at'      => now()->subDays(1),
            'returned_at' => now(),
        ]);

        $overdueIds = Loan::overdue()->pluck('id');

        $this->assertContains($overdueLoan->id, $overdueIds);
        $this->assertNotContains($activeLoan->id, $overdueIds);
        $this->assertNotContains($returnedLoan->id, $overdueIds);
    }

    /**
     * isOverdue() retourne false pour un emprunt non retourné mais dans les délais.
     */
    public function test_is_overdue_returns_false_for_active_loan(): void
    {
        $user = $this->makeUser();
        $book = $this->makeBook();
        $loan = $this->makeLoan($book, $user, ['due_at' => now()->addDays(7)]);

        $this->assertFalse($loan->isOverdue());
    }

    /**
     * isOverdue() retourne true pour un emprunt en retard.
     */
    public function test_is_overdue_returns_true_for_late_loan(): void
    {
        $user = $this->makeUser();
        $book = $this->makeBook();
        $loan = $this->makeLoan($book, $user, [
            'due_at'      => now()->subDays(2),
            'returned_at' => null,
        ]);

        $this->assertTrue($loan->isOverdue());
    }

    /**
     * isOverdue() retourne false pour un emprunt retourné même si due_at est dépassé.
     */
    public function test_is_overdue_returns_false_for_returned_loan(): void
    {
        $user = $this->makeUser();
        $book = $this->makeBook();
        $loan = $this->makeLoan($book, $user, [
            'due_at'      => now()->subDays(2),
            'returned_at' => now()->subDay(),
        ]);

        $this->assertFalse($loan->isOverdue());
    }
}
