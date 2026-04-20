<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
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
        'reserved_at',
        'notified_at',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime',
            'notified_at' => 'datetime',
        ];
    }

    /**
     * Le livre réservé.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * L'utilisateur qui a réservé.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope : réservations en attente.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'en_attente');
    }

    /**
     * Notifier la réservation : met à jour le statut et la date de notification.
     */
    public function notify(): void
    {
        $this->update([
            'status' => 'notifie',
            'notified_at' => now(),
        ]);
    }
}
