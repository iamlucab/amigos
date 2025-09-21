<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipCodeRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_code_request_id',
        'membership_code_id'
    ];

    public function membershipCodeRequest()
    {
        return $this->belongsTo(MembershipCodeRequest::class);
    }

    public function membershipCode()
    {
        return $this->belongsTo(MembershipCode::class);
    }
}