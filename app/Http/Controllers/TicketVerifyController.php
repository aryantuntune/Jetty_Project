<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketVerifyController extends Controller
{
    public function index(Request $request)
    {
        $ticket = null;

        if ($request->has('code')) {
            $ticket = Ticket::with(['branch', 'user', 'lines'])->find($request->code);
        }

        return view('tickets.verify', compact('ticket'));
    }

    public function verify(Request $request)
    {
        $ticket = Ticket::findOrFail($request->ticket_id);
        $ticket->verified_at = now();
        $ticket->save();

        return back()->with('success', 'Ticket verified successfully!');
    }
}
