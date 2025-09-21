<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'total_allocation',
        'max_level',
        'level_bonuses',
        'is_active',
        'description'
    ];

    protected $casts = [
        'level_bonuses' => 'array',
        'is_active' => 'boolean',
        'total_allocation' => 'decimal:2',
        'max_level' => 'integer'
    ];

    /**
     * Get the active configuration
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Calculate the bonus amount for a specific level
     */
    public function getBonusForLevel($level)
    {
        // If level is beyond max_level, return 0
        if ($level > $this->max_level) {
            return 0;
        }
        
        // If no custom levels are defined or level_bonuses is empty, distribute equally
        if (empty($this->level_bonuses)) {
            return round($this->total_allocation / $this->max_level, 2);
        }
        
        // Return custom amount if specified
        if (isset($this->level_bonuses[$level])) {
            return (float) $this->level_bonuses[$level];
        }

        // Calculate remaining amount to distribute
        $customTotal = 0;
        foreach ($this->level_bonuses as $amount) {
            $customTotal += (float) $amount;
        }
        
        $remaining = $this->total_allocation - $customTotal;
        
        // Count levels without custom amounts
        $customLevels = array_keys($this->level_bonuses);
        $autoLevels = 0;
        
        for ($i = 1; $i <= $this->max_level; $i++) {
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
     * Get all bonus amounts for all levels
     */
    public function getAllBonuses()
    {
        $bonuses = [];
        
        for ($i = 1; $i <= $this->max_level; $i++) {
            $bonuses[$i] = $this->getBonusForLevel($i);
        }
        
        return $bonuses;
    }
    
    /**
     * Validate that the total of all bonuses equals the total allocation
     */
    public function validateTotalMatches()
    {
        $bonuses = $this->getAllBonuses();
        $total = array_sum($bonuses);
        
        // Allow for small rounding differences
        return abs($total - $this->total_allocation) < 0.1;
    }
}
