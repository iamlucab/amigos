<?php


namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\RewardWinner;

class RewardHistoryController extends Controller
{
    public function index()
    {
        $member = Auth::user()->member;

       $rewards = RewardWinner::with('program')
    ->where('member_id', auth()->user()->member->id)
    ->latest('drawn_at')
    ->get();

        return view('members.rewards', compact('rewards'));
    }
}
