<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Member;
use App\Models\TicketReply;
use App\Models\User;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('member.user')->latest()->get();
        return view('admin.tickets.index', compact('tickets'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|in:pending,in_process,closed']);
        $ticket->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    public function show($id)
    {
        $ticket = Ticket::with(['member', 'replies.member', 'replies.admin'])->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
{
    $request->validate([
        'message' => 'required|string',
    ]);

    $ticket = Ticket::findOrFail($id);

    if ($ticket->status === 'closed') {
        return back()->with('error', 'This ticket is already closed.');
    }

    $user = auth()->user();

    TicketReply::create([
        'ticket_id'  => $ticket->id,
        'message'    => $request->message,
        'replied_by' => $user->role, // will be either "Admin" or "Member"
        'user_id'    => $user->id,
    ]);

    return back()->with('success', 'Reply sent successfully.');
}



    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_process,closed',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->status;
        $ticket->save();

        return back()->with('success', 'Ticket status updated successfully.');
    }
}
