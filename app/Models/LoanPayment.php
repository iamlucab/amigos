<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'amount',
        'paid_at',
        'due_date',
        'note',
        'notes',
        'is_paid',
        'payment_method',
        'reference_number',
        'payment_proof',
        'is_verified',
        'verified_at',
        'verified_by',
        'status'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_verified' => 'boolean',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}