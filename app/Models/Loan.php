<?php

namespace App\Models;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'book_id',
        'user_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'penalty_amount',
        'reminder_sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'borrowed_at'      => 'datetime',
            'due_at'           => 'datetime',
            'returned_at'      => 'datetime',
            'reminder_sent_at' => 'datetime',
            'penalty_amount'   => 'decimal:2',
        ];
    }

    /**
     * Le livre emprunté.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * L'utilisateur emprunteur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Les paiements de pénalité associés à cet emprunt.
     */
    public function penaltyPayments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenaltyPayment::class);
    }

    /**
     * Scope : emprunts en retard (due_at dépassé et non retourné).
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_at', '<', now())
                     ->whereNull('returned_at');
    }

    /**
     * Vérifie si l'emprunt est en retard.
     */
    public function isOverdue(): bool
    {
        return is_null($this->returned_at) && $this->due_at->isPast();
    }

    /**
     * Accessor : calcule automatiquement la pénalité en fonction
     * du nombre de jours de retard × tarif journalier (depuis Settings).
     */
    public function getPenaltyAmountAttribute($value): float
    {
        // Si l'emprunt n'est pas en retard, retourner 0
        if (!$this->isOverdue()) {
            return (float) ($value ?? 0);
        }

        // Calculer les jours de retard
        $daysOverdue = (int) $this->due_at->diffInDays(now());

        // Récupérer le tarif journalier depuis les Settings (fallback sur config)
        $dailyPenalty = (float) Setting::get(
            'daily_penalty',
            config('libraflow.daily_penalty', 100)
        );

        return $daysOverdue * $dailyPenalty;
    }
}
