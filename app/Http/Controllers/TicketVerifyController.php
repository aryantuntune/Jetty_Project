<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Contracts\Encryption\DecryptException;

class TicketVerifyController extends Controller
{
    public function index(Request $request)
    {
        $ticket = null;

        // If a ticket code is provided (encrypted)
        if ($request->has('code')) {
            try {
                // decrypt the code to get the real id
                $id = decrypt($request->code);
                // optionally ensure it's an integer
                $id = (int) $id;
                $ticket = Ticket::with('branch', 'user')->find($id);
            } catch (DecryptException $e) {
                // invalid/ tampered code, ignore or show an error message
                return redirect()->route('verify.index')
                                 ->with('error', 'Invalid or expired ticket code.');
            }
        }

        return view('tickets.verify', compact('ticket'));
    }

    public function verify(Request $request)
    {
        // ticket_id will be encrypted; decrypt it first
        try {
            $id = decrypt($request->ticket_id);
            $id = (int) $id;
        } catch (DecryptException $e) {
            return back()->with('error', 'Invalid ticket identifier.');
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->verified_at = now();
        $ticket->save();

        return back()->with('success', 'Ticket verified successfully!');
    }
}
