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
     * Role-based access:
     * - Super Admin (1): Sees all data across all branches and routes
     * - Admin (2): Sees all data across all branches and routes
     * - Manager (3): Sees data for their assigned ferry route only
     * - Operator (4): Sees data for their branch only
     * - Checker (5): Redirected to verify page (no dashboard access)
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Checker should only see verify page
        if ($user->role_id == 5) {
            return redirect()->route('verify.index');
        }

        // Get date filter parameters
        $viewMode = $request->get('view', 'day'); // 'day' or 'month'
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedMonth = $request->get('month', Carbon::today()->format('Y-m'));

        // Parse dates
        $date = Carbon::parse($selectedDate);
        $monthStart = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $monthEnd = Carbon::parse($selectedMonth . '-01')->endOfMonth();

        // Build base query with role-based filtering
        $ticketQuery = Ticket::query();
        $this->applyRoleFilter($ticketQuery, $user);

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

        // Get ferry boats count with role-based filtering
        $ferryBoatsQuery = FerryBoat::query();
        if ($user->role_id == 3 && $user->ferry_boat_id) {
            // Manager sees only their assigned ferry
            $ferryBoatsQuery->where('id', $user->ferry_boat_id);
        } elseif ($user->role_id == 4 && $user->branch_id) {
            // Operator sees only ferries in their branch
            $ferryBoatsQuery->where('branch_id', $user->branch_id);
        }
        $ferryBoatsCount = $ferryBoatsQuery->count();

        // Get recent tickets for activity feed
        $recentTicketsQuery = Ticket::with(['user', 'ferryBoat', 'branch']);
        $this->applyRoleFilter($recentTicketsQuery, $user);
        $recentTickets = $recentTicketsQuery
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
        $this->applyRoleFilter($prevTicketQuery, $user);

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

        // Get role name for display
        $roleNames = [
            1 => 'Super Admin',
            2 => 'Administrator',
            3 => 'Manager',
            4 => 'Operator',
            5 => 'Checker'
        ];
        $roleName = $roleNames[$user->role_id] ?? 'Staff';

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
            'monthStart',
            'roleName'
        ));
    }

    /**
     * Apply role-based filtering to a ticket query
     */
    private function applyRoleFilter($query, $user)
    {
        switch ($user->role_id) {
            case 1: // Super Admin - sees everything
            case 2: // Admin - sees everything
                // No filter needed
                break;

            case 3: // Manager - sees only their ferry route
                if ($user->ferry_boat_id) {
                    $query->where('ferry_boat_id', $user->ferry_boat_id);
                }
                break;

            case 4: // Operator - sees only their branch
                if ($user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                }
                break;

            default:
                // Unknown role - show nothing for safety
                $query->whereRaw('1 = 0');
                break;
        }

        return $query;
    }
}
