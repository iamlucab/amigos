<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    public function member() {
    return $this->belongsTo(Member::class);
}

    protected $fillable = [
        'member_id',
        'subject',
        'message',
        'status',
    ];

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'in_process' => 'In Process',
            'closed' => 'Closed',
            default => 'Unknown',
        };
    }



    public function replies()
{
    return $this->hasMany(TicketReply::class);
}



}
