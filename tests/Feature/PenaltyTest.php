<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenaltyTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function makeBook(): Book
    {
        return Book::factory()->create([
            'total_copies'     => 2,
            'available_copies' => 2,
        ]);
    }

    private function makeUser(): User
    {
        return User::factory()->create(['role' => 'lecteur']);
    }

    private function makeLoan(Book $book, User $user, Carbon $dueAt, ?Carbon $returnedAt = null): Loan
    {
        return Loan::create([
            'book_id'      => $book->id,
            'user_id'      => $user->id,
            'borrowed_at'  => $dueAt->copy()->subDays(14),
            'due_at'       => $dueAt,
            'returned_at'  => $returnedAt,
        ]);
    }

    // ─── Tests ─────────────────────────────────────────────────────────────────

    /**
     * Aucune pénalité si le livre est rendu dans les délais.
     */
    public function test_no_penalty_when_returned_on_time(): void
    {
        $user    = $this->makeUser();
        $book    = $this->makeBook();
        $service = app(LoanService::class);

        $loan = $this->makeLoan($book, $user, now()->addDays(3)); // Pas encore échu

        $this->assertEquals(0, $service->calculatePenalty($loan));
    }

    /**
     * Pénalité correcte : 3 jours × tarif journalier.
     */
    public function test_penalty_calculated_for_3_days_overdue(): void
    {
        $user    = $this->makeUser();
        $book    = $this->makeBook();
        $service = app(LoanService::class);

        $dailyRate = (float) Setting::get('daily_penalty', config('libraflow.daily_penalty', 100));

        $loan = $this->makeLoan($book, $user, now()->subDays(3));

        $penalty = $service->calculatePenalty($loan);

        $this->assertEquals(3 * $dailyRate, $penalty);
    }

    /**
     * Pénalité correcte : 10 jours × tarif journalier.
     */
    public function test_penalty_calculated_for_10_days_overdue(): void
    {
        $user    = $this->makeUser();
        $book    = $this->makeBook();
        $service = app(LoanService::class);

        $dailyRate = (float) Setting::get('daily_penalty', config('libraflow.daily_penalty', 100));

        $loan = $this->makeLoan($book, $user, now()->subDays(10));

        $penalty = $service->calculatePenalty($loan);

        $this->assertEquals(10 * $dailyRate, $penalty);
    }

    /**
     * Aucune pénalité si un livre en retard est quand même retourné (returned_at non null).
     */
    public function test_no_penalty_when_loan_is_returned_even_if_late(): void
    {
        $user    = $this->makeUser();
        $book    = $this->makeBook();
        $service = app(LoanService::class);

        // Retourné hier, était en retard de 2 jours
        $loan = $this->makeLoan(
            $book, $user,
            now()->subDays(2),
            now()->subDay() // Rendu hier
        );

        // isOverdue() doit être false (car returned_at non null)
        $this->assertFalse($loan->isOverdue());
        $this->assertEquals(0, $service->calculatePenalty($loan));
    }

    /**
     * Le tarif journalier depuis Setting écrase la valeur par défaut de config.
     */
    public function test_penalty_uses_setting_over_config(): void
    {
        $user    = $this->makeUser();
        $book    = $this->makeBook();
        $service = app(LoanService::class);

        // Définir un tarif personnalisé
        Setting::set('daily_penalty', 250);

        $loan = $this->makeLoan($book, $user, now()->subDays(4));

        $penalty = $service->calculatePenalty($loan);

        $this->assertEquals(4 * 250, $penalty);
    }

    /**
     * getPenaltyAmountAttribute retourne 0 pour un emprunt non en retard.
     */
    public function test_penalty_amount_accessor_returns_zero_when_not_overdue(): void
    {
        $user = $this->makeUser();
        $book = $this->makeBook();

        $loan = $this->makeLoan($book, $user, now()->addDays(5));

        $this->assertEquals(0.0, (float) $loan->penalty_amount);
    }

    /**
     * getPenaltyAmountAttribute calcule correctement les jours × tarif.
     */
    public function test_penalty_amount_accessor_calculates_correctly(): void
    {
        $user = $this->makeUser();
        $book = $this->makeBook();

        $dailyRate = (float) Setting::get('daily_penalty', config('libraflow.daily_penalty', 100));

        $loan = $this->makeLoan($book, $user, now()->subDays(5));

        // L'accesseur calcule dynamiquement
        $this->assertEquals(5 * $dailyRate, (float) $loan->penalty_amount);
    }
}
