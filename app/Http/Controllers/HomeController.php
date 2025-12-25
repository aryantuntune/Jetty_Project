<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\FerryBoat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get date filter parameters
        $viewMode = $request->get('view', 'day'); // 'day' or 'month'
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedMonth = $request->get('month', Carbon::today()->format('Y-m'));

        // Parse dates
        $date = Carbon::parse($selectedDate);
        $monthStart = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $monthEnd = Carbon::parse($selectedMonth . '-01')->endOfMonth();

        // Build base query with branch filter for non-admins
        $ticketQuery = Ticket::query();
        if (!in_array($user->role_id, [1, 2])) {
            // Operators only see their branch data
            $ticketQuery->where('branch_id', $user->branch_id);
        }

        // Apply date filter based on view mode
        if ($viewMode === 'month') {
            $ticketQuery->whereBetween('created_at', [$monthStart, $monthEnd]);
            $periodLabel = $monthStart->format('F Y');
        } else {
            $ticketQuery->whereDate('created_at', $date);
            $periodLabel = $date->format('d M Y');
        }

        // Calculate metrics
        $ticketsCount = (clone $ticketQuery)->count();
        $totalRevenue = (clone $ticketQuery)->sum('total_amount');
        $pendingVerifications = (clone $ticketQuery)->whereNull('verified_at')->count();

        // Get ferry boats count
        $ferryBoatsQuery = FerryBoat::query();
        if (!in_array($user->role_id, [1, 2])) {
            $ferryBoatsQuery->where('branch_id', $user->branch_id);
        }
        $ferryBoatsCount = $ferryBoatsQuery->count();

        // Get recent tickets for activity feed
        $recentTickets = Ticket::with(['user', 'ferryBoat', 'branch'])
            ->when(!in_array($user->role_id, [1, 2]), function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate comparison with previous period
        if ($viewMode === 'month') {
            $prevStart = $monthStart->copy()->subMonth();
            $prevEnd = $monthEnd->copy()->subMonth();
        } else {
            $prevStart = $date->copy()->subDay();
            $prevEnd = $prevStart->copy();
        }

        $prevTicketQuery = Ticket::query();
        if (!in_array($user->role_id, [1, 2])) {
            $prevTicketQuery->where('branch_id', $user->branch_id);
        }

        if ($viewMode === 'month') {
            $prevTicketQuery->whereBetween('created_at', [$prevStart, $prevEnd]);
        } else {
            $prevTicketQuery->whereDate('created_at', $prevStart);
        }

        $prevTicketsCount = $prevTicketQuery->count();
        $prevRevenue = (clone $prevTicketQuery)->sum('total_amount');

        // Calculate percentage changes
        $ticketsChange = $prevTicketsCount > 0
            ? round((($ticketsCount - $prevTicketsCount) / $prevTicketsCount) * 100, 1)
            : ($ticketsCount > 0 ? 100 : 0);
        $revenueChange = $prevRevenue > 0
            ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : ($totalRevenue > 0 ? 100 : 0);

        return view('home', compact(
            'ticketsCount',
            'totalRevenue',
            'ferryBoatsCount',
            'pendingVerifications',
            'recentTickets',
            'viewMode',
            'selectedDate',
            'selectedMonth',
            'periodLabel',
            'ticketsChange',
            'revenueChange',
            'date',
            'monthStart'
        ));
    }
}
