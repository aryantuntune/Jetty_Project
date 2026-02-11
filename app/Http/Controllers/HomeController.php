<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\FerryBoat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

        // Build optimized query - only fetch what we need for the specific date/period
        $ticketQuery = Ticket::query()
            ->whereNull('guest_id');

        // Apply role-based filtering
        $this->applyRoleFilter($ticketQuery, $user);

        // Apply date filter based on view mode
        if ($viewMode === 'month') {
            $ticketQuery->whereBetween('ticket_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
            $periodLabel = $monthStart->format('F Y');
        } else {
            $ticketQuery->where('ticket_date', $date->toDateString());
            $periodLabel = $date->format('d M Y');
        }

        // Get metrics with single optimized query (uses index on ticket_date)
        $metrics = (clone $ticketQuery)
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_amount), 0) as revenue')
            ->first();

        $ticketsCount = $metrics->cnt ?? 0;
        $totalRevenue = $metrics->revenue ?? 0;
        $pendingVerifications = 0;

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

        // Get recent tickets for activity feed - only last 5, very fast query
        $recentTicketsQuery = Ticket::with(['user:id,name', 'ferryBoat:id,name', 'branch:id,branch_name'])
            ->select('id', 'ticket_date', 'ticket_no', 'total_amount', 'payment_mode', 'user_id', 'ferry_boat_id', 'branch_id', 'created_at', 'verified_at');
        $this->applyRoleFilter($recentTicketsQuery, $user);
        $recentTickets = $recentTicketsQuery
            ->orderByDesc('id')  // Use primary key for fastest ordering
            ->limit(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_no' => $ticket->ticket_no,
                    'total_amount' => $ticket->total_amount,
                    'ticket_date' => $ticket->ticket_date,
                    'verified_at' => $ticket->verified_at,
                    'ferry_name' => $ticket->ferryBoat->name ?? 'N/A',
                    'operator_name' => $ticket->user->name ?? 'Unknown',
                    'created_at_human' => $ticket->created_at->diffForHumans(),
                ];
            });

        // Skip comparison queries for performance - just show 0% change
        $prevTicketsCount = 0;
        $prevRevenue = 0;

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

        // Determine scope info  
        $scopeType = in_array($user->role_id, [1, 2]) ? 'global' : ($user->role_id == 3 ? 'route' : 'branch');
        $scope = $user->role_id == 3
            ? ($user->ferryBoat->name ?? 'Unknown Route')
            : ($user->branch->branch_name ?? 'Unknown Branch');

        return Inertia::render('Dashboard', [
            'stats' => [
                'ticketsCount' => $ticketsCount,
                'totalRevenue' => (float) $totalRevenue,
                'ferryBoatsCount' => $ferryBoatsCount,
                'pendingVerifications' => $pendingVerifications,
                'ticketsChange' => $ticketsChange,
                'revenueChange' => $revenueChange,
            ],
            'filters' => [
                'viewMode' => $viewMode,
                'selectedDate' => $selectedDate,
                'selectedMonth' => $selectedMonth,
                'periodLabel' => $periodLabel,
                'isToday' => $date->isToday(),
                'isCurrentMonth' => $monthStart->isSameMonth(Carbon::now()),
                'prevDate' => $date->copy()->subDay()->format('Y-m-d'),
                'nextDate' => $date->copy()->addDay()->format('Y-m-d'),
                'prevMonth' => $monthStart->copy()->subMonth()->format('Y-m'),
                'nextMonth' => $monthStart->copy()->addMonth()->format('Y-m'),
            ],
            'recentTickets' => $recentTickets,
            'userContext' => [
                'roleName' => $roleName,
                'scopeType' => $scopeType,
                'scope' => $scope,
            ],
            'routes' => [
                'ticketEntry' => route('ticket-entry.create'),
                'reports' => route('reports.tickets'),
                'guests' => route('guests.index'),
                'ferryBoats' => route('ferryboats.index'),
            ],
        ]);
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
