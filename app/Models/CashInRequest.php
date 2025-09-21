<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashInRequest extends Model
{
    protected $fillable = [
        'member_id', 
        'amount', 
        'note', 
        'payment_method',
        'proof_path',
        'status'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
