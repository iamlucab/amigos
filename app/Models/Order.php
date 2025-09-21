<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $fillable = [
    'member_id',
    'total_amount',
    'total_cashback',
    'status',
    'cashback_given',
    'payment_method',
    'delivery_type',
    'delivery_address',
    'contact_number',
    'reference_image',
    'gcash_note',
    'bank_note',
    'amount_sent',
    'promo_code',
    'promo_discount',
];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }


    
}
