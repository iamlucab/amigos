<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'cashback', 'status'];
    
    // Status constants
    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROCESS = 'In process';
    const STATUS_ON_THE_WAY = 'On the Way';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_CANCELLED = 'Cancelled';
    
    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROCESS,
            self::STATUS_ON_THE_WAY,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
