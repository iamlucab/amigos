<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Member;
use App\Models\TicketReply;

class TicketController extends Controller
{
    public function index()
    {
        $member = auth()->user()->member;
        $tickets = $member->tickets()->latest()->get();

        return view('members.tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $member = auth()->user()->member;

        Ticket::create([
            'member_id' => $member->id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', '✅ Your ticket has been submitted successfully!');
    }

public function show($id)
{
    $ticket = Ticket::with(['replies.member', 'replies.admin'])->findOrFail($id);
    return view('members.tickets.show', compact('ticket'));
}

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $member = auth()->user()->member;
        $ticket = Ticket::where('member_id', $member->id)->findOrFail($id);

        if ($ticket->status === 'closed') {
            return redirect()->back()->with('error', '⚠️ This ticket is already closed and cannot be replied to.');
        }
TicketReply::create([
    'ticket_id' => $ticket->id,
    'message' => $request->message,
    'replied_by' => 'member',
    'member_id' => auth()->user()->member->id,
]);

        return redirect()->back()->with('success', '✉️ Your reply has been sent!');
    }
}
