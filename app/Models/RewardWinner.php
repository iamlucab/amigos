<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardWinner extends Model {
    protected $fillable = ['reward_program_id', 'member_id', 'drawn_at', 'excluded_until'];

   
public function program()
{
    return $this->belongsTo(\App\Models\RewardProgram::class, 'reward_program_id');
}

public function member()
{
    return $this->belongsTo(Member::class);
}

    
}
