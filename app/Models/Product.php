<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'cashback_amount',
        'cashback_max_level',
        'cashback_level_bonuses',
        'stock_quantity',
        'category_id',
        'unit_id',
        'thumbnail',
        'gallery',
        'attributes',
        'discount_value',
        'discount_type',
        'promo_code',
        'created_by',
    ];

    protected $casts = [
        'cashback_level_bonuses' => 'array',
        'attributes' => 'array',
    ];

    /**
     * Mutator for created_by field to handle phone number to user ID conversion
     */
    public function setCreatedByAttribute($value)
    {
        // If the value looks like a phone number (11 digits), convert it to user ID
        if (is_string($value) && preg_match('/^[0-9]{11}$/', $value)) {
            $user = \App\Models\User::where('mobile_number', $value)->first();
            if ($user) {
                $this->attributes['created_by'] = $user->id;
                return;
            }
        }
        
        // Otherwise, use the value as-is
        $this->attributes['created_by'] = $value;
    }
    
    /**
     * Accessor for created_by field to ensure it returns the correct value
     */
    public function getCreatedByAttribute($value)
    {
        return $value;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Calculate the bonus amount for a specific level
     */
    public function getCashbackForLevel($level)
    {
        // If level is beyond max_level, return 0
        if ($level > $this->cashback_max_level) {
            return 0;
        }
        
        // If no custom levels are defined or cashback_level_bonuses is empty, distribute equally
        if (empty($this->cashback_level_bonuses)) {
            return round($this->cashback_amount / $this->cashback_max_level, 2);
        }
        
        // Return custom amount if specified
        if (isset($this->cashback_level_bonuses[$level])) {
            return (float) $this->cashback_level_bonuses[$level];
        }

        // Calculate remaining amount to distribute
        $customTotal = 0;
        foreach ($this->cashback_level_bonuses as $amount) {
            $customTotal += (float) $amount;
        }
        
        $remaining = $this->cashback_amount - $customTotal;
        
        // Count levels without custom amounts
        $customLevels = array_keys($this->cashback_level_bonuses);
        $autoLevels = 0;
        
        for ($i = 1; $i <= $this->cashback_max_level; $i++) {
            if (!in_array((string)$i, $customLevels)) {
                $autoLevels++;
            }
        }
        
        // If no levels to auto-distribute, return 0
        if ($autoLevels === 0) {
            return 0;
        }
        
        // Distribute remaining amount equally
        return round($remaining / $autoLevels, 2);
    }

    /**
     * Get all cashback amounts for all levels
     */
    public function getAllCashbacks()
    {
        $cashbacks = [];
        
        for ($i = 1; $i <= $this->cashback_max_level; $i++) {
            $cashbacks[$i] = $this->getCashbackForLevel($i);
        }
        
        return $cashbacks;
    }
    
    /**
     * Get a formatted string of the cashback levels for display
     */
    public function getCashbackLevelsDisplay()
    {
        return "Cashback ₱{$this->cashback_amount} (Level 1 to Level {$this->cashback_max_level})";
    }

    /**
     * Check if product has a discount
     */
    public function hasDiscount()
    {
        return !empty($this->discount_value) && $this->discount_value > 0;
    }

    /**
     * Calculate the discounted price
     */
    public function getDiscountedPrice()
    {
        if (!$this->hasDiscount()) {
            return $this->price;
        }

        if ($this->discount_type === 'percentage') {
            $discount = $this->price * ($this->discount_value / 100);
            return $this->price - $discount;
        } else {
            // Fixed amount discount
            return max(0, $this->price - $this->discount_value);
        }
    }

    /**
     * Get the discount amount
     */
    public function getDiscountAmount()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $this->price * ($this->discount_value / 100);
        } else {
            return min($this->discount_value, $this->price);
        }
    }

    /**
     * Get discount percentage (for display)
     */
    public function getDiscountPercentage()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $this->discount_value;
        } else {
            // Calculate percentage for fixed amount
            return round(($this->getDiscountAmount() / $this->price) * 100);
        }
    }
    
    /**
     * Ensure gallery is always returned as an array
     */
    public function getGalleryAttribute($value)
    {
        // If gallery is already an array, return it as is
        if (is_array($value)) {
            return $value;
        }
        
        // If gallery is null, return empty array
        if (is_null($value)) {
            return [];
        }
        
        // If gallery is a string, try to decode it as JSON
        if (is_string($value)) {
            // Handle case where value might be double-encoded
            if (substr($value, 0, 1) === '"' && substr($value, -1) === '"') {
                // Remove outer quotes and decode again
                $value = json_decode($value, true);
                if (is_array($value)) {
                    return $value;
                }
            }
            
            // Try to decode as JSON
            $decoded = json_decode($value, true);
            // If decoding was successful and result is an array, return it
            if (is_array($decoded)) {
                return $decoded;
            }
            // If it's a single image path, return it as an array
            if (!empty($value)) {
                return [$value];
            }
        }
        
        // Default to empty array
        return [];
    }
}
