<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'amount',
        'interest_rate',
        'monthly_payment',
        'purpose',
        'status',
        'term_months',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'approved_at',
        'rejected_at',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }


public function getMonthlyDueAttribute()
{
    $total = $this->amount + ($this->amount * ($this->interest_rate / 100));
    $months = $this->term_months ?? 6;
    return round($total / $months, 2);
}


public function payments()
{
    return $this->hasMany(LoanPayment::class);
}


}
