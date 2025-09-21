<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'address', 'precinct', 'class',
        'region', 'province', 'city', 'barangay'
    ];

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}