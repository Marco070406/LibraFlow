<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenaltyPayment extends Model
{
    protected $fillable = [
        'loan_id',
        'user_id',
        'amount_paid',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_at'     => 'datetime',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
