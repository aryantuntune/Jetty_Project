<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketVerifyController extends Controller
{
  public function index(Request $request)
{
    $ticket = null;
    $user = Auth::user();

    if ($request->has('code')) {

        // Admin / Manager → can access all branch tickets
        if (in_array($user->role_id, [1, 2])) {
            $ticket = Ticket::with(['branch', 'user', 'lines'])
                ->where('id', $request->code)
                ->first();
        } 
        // Other users → restrict to their own branch
        else {
            $ticket = Ticket::with(['branch', 'user', 'lines'])
                ->where('id', $request->code)
                ->where('branch_id', $user->branch_id)
                ->first();
        }

        if (!$ticket) {
            return back()->with('error', 'Invalid Ticket ID or not allowed for your branch!');
        }
    }

    return view('tickets.verify', compact('ticket'));
}




   public function verify(Request $request)
{
    $user = Auth::user();

    // Admin / Manager → can verify any branch ticket
    if (in_array($user->role_id, [1, 2])) {
        $ticket = Ticket::findOrFail($request->ticket_id);
    } 
    // Other users → can verify only their branch tickets
    else {
        $ticket = Ticket::where('id', $request->ticket_id)
            ->where('branch_id', $user->branch_id)
            ->firstOrFail();
    }

    $ticket->verified_at = now();
    $ticket->checker_id = $user->id;
    $ticket->save();

    return back()->with('success', 'Ticket verified successfully!');
}


}
