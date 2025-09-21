<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbackLog extends Model
{
    protected $fillable = [
        'member_id',
        'order_id',
        'product_id',
        'amount',
        'description',
        'source',
        'level',
    ];
    

    /**
     * Get the member associated with this cashback log.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the order associated with this cashback log (if any).
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the product associated with this cashback log (if any).
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
