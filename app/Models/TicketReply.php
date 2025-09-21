<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'message',
        'replied_by',
        'member_id',
        'user_id', // ✅ Add this
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function member()
    {
        return $this->belongsTo(\App\Models\Member::class, 'member_id');
    }

    public function user()
{
    return $this->belongsTo(User::class);
}

    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function getRepliedByAttribute($value)
    {
        return match ($value) {
            'admin' => 'Admin',
            'member' => 'Member',
            default => ucfirst($value),
        };
    }
}
