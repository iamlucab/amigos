<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardProgram extends Model {
    protected $fillable = ['title', 'description', 'draw_date'];
   

public function winner()
{
    return $this->belongsTo(RewardWinner::class, 'winner_id');
}

protected $casts = [
    'draw_date' => 'datetime',
];

public function winners() {
    return $this->hasMany(RewardWinner::class);
}
}