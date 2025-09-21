<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\RewardWinner;
use App\Models\RewardProgram;
use Carbon\Carbon;

class RewardController extends Controller

{
public function index() {
    $programs = RewardProgram::with('winners.member')->latest()->get();
    return view('admin.rewards.index', compact('programs'));
}

public function create() {
    return view('admin.rewards.create');
}

public function store(Request $request) {
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required',
        'draw_date' => 'required|date',
    ]);
    RewardProgram::create($request->all());
   return redirect()->route('admin.rewards.index')->with('success', 'Reward program created.');
}

public function draw(Request $request, RewardProgram $program)
{
    $count = $request->input('count', 1);

    $excludedIds = RewardWinner::where('excluded_until', '>', now())->pluck('member_id');
    
    

    $excludedMembers = Member::whereHas('user', function($q) {
    $q->where('role', '!=', 'Member'); // Exclude Admins, Staff
})->pluck('id');

$allExcludedIds = $excludedIds->merge($excludedMembers);

$eligibleMembers = Member::whereNotIn('id', $allExcludedIds)
                         ->inRandomOrder()
                         ->limit($count)
                         ->get();


    foreach ($eligibleMembers as $member) {
        RewardWinner::create([
            'reward_program_id' => $program->id,
            'member_id' => $member->id,
            'drawn_at' => now(),
            'excluded_until' => now()->addMonths(3),
            'status' => 'unclaimed', 
        ]);
    }

    return redirect()->route('admin.rewards.index')->with('success', "$eligibleMembers->count() winners picked.");
}

public function pickWinner(Request $request, $id)
{
    $program = RewardProgram::findOrFail($id);

    $count = (int) $request->input('count', 1); // Number of winners to pick

    // Exclude members who won in the last 3 months
    $excludedIds = RewardWinner::where('excluded_until', '>', now())->pluck('member_id');

    // Random eligible members
    $eligibleMembers = Member::whereNotIn('id', $excludedIds)
                            ->inRandomOrder()
                            ->limit($count)
                            ->get();

    if ($eligibleMembers->isEmpty()) {
        return back()->with('error', 'No eligible members found.');
    }

    foreach ($eligibleMembers as $member) {
        RewardWinner::create([
            'reward_program_id' => $program->id,
            'member_id' => $member->id,
            'drawn_at' => now(),
            'excluded_until' => now()->addMonths(3),
               'status' => 'unclaimed', 
        ]);
    }

    return back()->with('success', $eligibleMembers->count() . ' winners picked!');
}

 public function winners()
    {
        $winners = RewardWinner::with(['program', 'member'])->latest()->get();
        return view('admin.rewards.winners', compact('winners'));
    }


public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:unclaimed,redeemed,expired',
    ]);

    $winner = RewardWinner::findOrFail($id);
    $winner->status = $request->status;
    $winner->save();

    return back()->with('success', 'Winner status updated successfully.');
}



}
