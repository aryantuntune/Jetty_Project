<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FerryBoat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;


class TicketReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Branch filtering based on role
        if (in_array($user->role_id, [1, 2])) {
            // Admin / Manager → show all branches
            $branches = Branch::all();
            $branchRestriction = null;
        } else {
            // Other users → only their branch
            $branches = Branch::where('id', $user->branch_id)->get();
            $branchRestriction = $user->branch_id;
        }

        // Filter dropdowns
        $ferryBoats = FerryBoat::all();
        $paymentModes = ['CASH MEMO', 'CREDIT MEMO', 'GUEST PASS', 'GPay'];
        $ferryTypes = ['REGULAR', 'SPECIAL'];

        // Query filters from request
        $branchId = $request->query('branch_id');
        $paymentMode = $request->query('payment_mode');
        $ferryType = $request->query('ferry_type');
        $ferryBoatId = $request->query('ferry_boat_id');

        // Default to today's date if no date filter provided (prevents scanning all 2M+ records)
        $dateFrom = $request->query('date_from') ?? now()->toDateString();
        $dateTo = $request->query('date_to') ?? now()->toDateString();

        // Tickets Query - use ticket_date for filtering (works for both legacy and new tickets)
        $query = Ticket::with('branch', 'ferryBoat')
            ->where('guest_id', null)

            // Restrict to login user's branch if not admin/manager
            ->when($branchRestriction, fn($q) => $q->where('branch_id', $branchRestriction))

            // Filters
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
            ->when($ferryType, fn($q) => $q->where('ferry_type', $ferryType))
            ->when($ferryBoatId, fn($q) => $q->where('ferry_boat_id', $ferryBoatId))
            // Use created_at for date filtering (ticket_date column missing)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at', 'desc')
            ->orderBy('ticket_no', 'desc');

        // Use simplePaginate - doesn't count total rows (MUCH faster)
        $tickets = $query->simplePaginate(25);

        // Calculate total for the filtered date range using optimized query
        // This uses the index on ticket_date for fast filtering
        $totalAmount = (clone $query)->sum('total_amount');

        return Inertia::render('Reports/Tickets/Index', [
            'tickets' => $tickets,
            'branches' => $branches,
            'ferryBoats' => $ferryBoats,
            'paymentModes' => $paymentModes,
            'ferryTypes' => $ferryTypes,
            'branchId' => $branchId,
            'paymentMode' => $paymentMode,
            'ferryType' => $ferryType,
            'ferryBoatId' => $ferryBoatId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalAmount' => $totalAmount,
        ]);
    }


    public function getFerryBoatsByBranch($branchId)
    {
        $ferryBoats = FerryBoat::where('branch_id', $branchId)->get();
        return response()->json($ferryBoats);
    }

    // app/Http/Controllers/TicketReportController.php


    public function vehicleWiseIndex(Request $request)
    {
        $branches = Branch::orderBy('branch_name')->get();
        $ferryBoats = FerryBoat::all();
        $paymentModes = ['CASH MEMO', 'CREDIT MEMO', 'GUEST PASS', 'GPay'];
        $ferryTypes = ['REGULAR', 'SPECIAL'];

        $branchId = $request->query('branch_id');
        $paymentMode = $request->query('payment_mode');
        $ferryType = $request->query('ferry_type');
        $ferryBoatId = $request->query('ferry_boat_id');
        // Default to today's date if no date filter provided (prevents scanning all 2M+ records)
        $dateFrom = $request->query('date_from') ?? now()->toDateString();
        $dateTo = $request->query('date_to') ?? now()->toDateString();
        $vehicleName = trim((string) $request->query('vehicle_name'));
        $vehicleNo = trim((string) $request->query('vehicle_no'));

        // Base filter over tickets - optimized for large datasets
        // Base filter over tickets - optimized for large datasets
        $filter = Ticket::query()
            ->where('guest_id', null)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
            ->when($ferryType, fn($q) => $q->where('ferry_type', $ferryType))
            ->when($ferryBoatId, fn($q) => $q->where('ferry_boat_id', $ferryBoatId))
            ->when($vehicleNo, fn($q) => $q->whereHas('lines', fn($l) => $l->where('vehicle_no', 'like', "%{$vehicleNo}%")))
            ->when($vehicleName, fn($q) => $q->whereHas('lines', fn($l) => $l->where('vehicle_name', 'like', "%{$vehicleName}%")));

        // Grid query with subselects for a representative vehicle_no/name (MIN)
        $base = $filter
            ->with(['branch', 'ferryBoat'])
            ->select('tickets.*')
            ->selectSub(function ($q) {
                $q->from('ticket_lines as tl')
                    ->whereColumn('tl.ticket_id', 'tickets.id')
                    ->selectRaw('MIN(tl.vehicle_no)');
            }, 'vehicle_no_join')
            ->selectSub(function ($q) {
                $q->from('ticket_lines as tl')
                    ->whereColumn('tl.ticket_id', 'tickets.id')
                    ->selectRaw('MIN(tl.vehicle_name)');
            }, 'vehicle_name_join')
            ->orderBy('created_at', 'desc')
            ->orderBy('ticket_no', 'desc');

        // Use simplePaginate - doesn't count total rows (MUCH faster)
        $tickets = $base->simplePaginate(25);

        // Calculate total for the filtered date range
        $totalAmount = (clone $filter)->sum('total_amount');
        $pageTotalAmount = $tickets->sum('total_amount');

        return Inertia::render('Reports/VehicleTickets/Index', [
            'tickets' => $tickets,
            'branches' => $branches,
            'ferryBoats' => $ferryBoats,
            'paymentModes' => $paymentModes,
            'ferryTypes' => $ferryTypes,
            'branchId' => $branchId,
            'paymentMode' => $paymentMode,
            'ferryType' => $ferryType,
            'ferryBoatId' => $ferryBoatId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'vehicleName' => $vehicleName,
            'vehicleNo' => $vehicleNo,
            'totalAmount' => $totalAmount,
            'pageTotalAmount' => $pageTotalAmount,
        ]);
    }



    private function buildTicketsFilter(Request $request)
    {
        $branchId = $request->query('branch_id');
        $paymentMode = $request->query('payment_mode');
        $ferryType = $request->query('ferry_type');
        $ferryBoatId = $request->query('ferry_boat_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        return Ticket::query()
            ->with(['branch', 'ferryBoat'])
            ->where('guest_id', null)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
            ->when($ferryType, fn($q) => $q->where('ferry_type', $ferryType))
            ->when($ferryBoatId, fn($q) => $q->where('ferry_boat_id', $ferryBoatId))
            // Use ticket_date for historical data, fallback to created_at if ticket_date is null
            ->when($dateFrom, fn($q) => $q->where(function ($q) use ($dateFrom) {
                $q->whereDate('ticket_date', '>=', $dateFrom)
                    ->orWhere(fn($q) => $q->whereNull('ticket_date')->whereDate('created_at', '>=', $dateFrom));
            }))
            ->when($dateTo, fn($q) => $q->where(function ($q) use ($dateTo) {
                $q->whereDate('ticket_date', '<=', $dateTo)
                    ->orWhere(fn($q) => $q->whereNull('ticket_date')->whereDate('created_at', '<=', $dateTo));
            }))
            ->orderByRaw('COALESCE(ticket_date, DATE(created_at)) DESC, ticket_no DESC');
    }

    private function buildVehicleTicketsFilter(Request $request)
    {
        $branchId = $request->query('branch_id');
        $paymentMode = $request->query('payment_mode');
        $ferryType = $request->query('ferry_type');
        $ferryBoatId = $request->query('ferry_boat_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $vehicleName = trim((string) $request->query('vehicle_name'));
        $vehicleNo = trim((string) $request->query('vehicle_no'));

        return Ticket::query()
            ->with(['branch', 'ferryBoat'])
            ->where('guest_id', null)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
            ->when($ferryType, fn($q) => $q->where('ferry_type', $ferryType))
            ->when($ferryBoatId, fn($q) => $q->where('ferry_boat_id', $ferryBoatId))
            // Use ticket_date for historical data, fallback to created_at if ticket_date is null
            ->when($dateFrom, fn($q) => $q->where(function ($q) use ($dateFrom) {
                $q->whereDate('ticket_date', '>=', $dateFrom)
                    ->orWhere(fn($q) => $q->whereNull('ticket_date')->whereDate('created_at', '>=', $dateFrom));
            }))
            ->when($dateTo, fn($q) => $q->where(function ($q) use ($dateTo) {
                $q->whereDate('ticket_date', '<=', $dateTo)
                    ->orWhere(fn($q) => $q->whereNull('ticket_date')->whereDate('created_at', '<=', $dateTo));
            }))
            ->when($vehicleNo, fn($q) => $q->whereHas('lines', fn($l) => $l->where('vehicle_no', 'like', "%{$vehicleNo}%")))
            ->when($vehicleName, fn($q) => $q->whereHas('lines', fn($l) => $l->where('vehicle_name', 'like', "%{$vehicleName}%")))
            ->orderByRaw('COALESCE(ticket_date, DATE(created_at)) DESC, ticket_no DESC');
    }

    /**
     * GET /reports/tickets/export  → CSV export for Ticket Details
     */
    public function exportTicketsCsv(Request $request): StreamedResponse
    {
        $filename = 'tickets_' . now('Asia/Kolkata')->format('Ymd_His') . '.csv';

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');

            // Header row (include legacy ticket number and customer fields)
            fputcsv($handle, [
                'Ticket Date',
                'Ticket No',
                'System ID',
                'Branch',
                'Payment Mode',
                'Boat Name',
                'Ferry Time',
                'Ferry Type',
                'Customer Name',
                'Customer Mobile',
                'Total Amount',
                'Net Amount'
            ]);

            $this->buildTicketsFilter($request)
                ->chunkById(1000, function ($chunk) use ($handle) {
                    foreach ($chunk as $t) {
                        // Use ticket_date for legacy tickets, created_at for new ones
                        $ticketDate = $t->ticket_date
                            ? $t->ticket_date->format('Y-m-d')
                            : optional($t->created_at)->timezone('Asia/Kolkata')->format('Y-m-d');

                        fputcsv($handle, [
                            $ticketDate,
                            $t->ticket_no ?? '',
                            $t->id,
                            $t->branch->branch_name ?? '',
                            $t->payment_mode,
                            $t->ferryBoat->name ?? '',
                            optional($t->ferry_time)->timezone('Asia/Kolkata')->format('H:i'),
                            $t->ferry_type,
                            $t->customer_name ?? '',
                            $t->customer_mobile ?? '',
                            number_format((float) $t->total_amount, 2, '.', ''),
                            number_format((float) $t->net_amount, 2, '.', ''),
                        ]);
                    }
                }, 'id');

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * GET /reports/vehicle-tickets/export → CSV export for Vehicle-wise page
     * (includes a representative vehicle no/name via MIN from lines if present)
     */
    public function exportVehicleTicketsCsv(Request $request): StreamedResponse
    {
        $filename = 'vehicle_tickets_' . now('Asia/Kolkata')->format('Ymd_His') . '.csv';

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Ticket Date',
                'Ticket No',
                'System ID',
                'Branch',
                'Payment Mode',
                'Boat Name',
                'Ferry Time',
                'Ferry Type',
                'Vehicle Name',
                'Vehicle No',
                'Customer Name',
                'Customer Mobile',
                'Total Amount',
                'Net Amount'
            ]);

            // base with subselects for vehicle fields
            $q = $this->buildVehicleTicketsFilter($request)
                ->select('tickets.*')
                ->selectSub(function ($q) {
                    $q->from('ticket_lines as tl')
                        ->whereColumn('tl.ticket_id', 'tickets.id')
                        ->selectRaw('MIN(tl.vehicle_no)');
                }, 'vehicle_no_join')
                ->selectSub(function ($q) {
                    $q->from('ticket_lines as tl')
                        ->whereColumn('tl.ticket_id', 'tickets.id')
                        ->selectRaw('MIN(tl.vehicle_name)');
                }, 'vehicle_name_join');

            $q->chunkById(1000, function ($chunk) use ($handle) {
                foreach ($chunk as $t) {
                    // Use ticket_date for legacy tickets, created_at for new ones
                    $ticketDate = $t->ticket_date
                        ? $t->ticket_date->format('Y-m-d')
                        : optional($t->created_at)->timezone('Asia/Kolkata')->format('Y-m-d');

                    fputcsv($handle, [
                        $ticketDate,
                        $t->ticket_no ?? '',
                        $t->id,
                        $t->branch->branch_name ?? '',
                        $t->payment_mode,
                        $t->ferryBoat->name ?? '',
                        optional($t->ferry_time)->timezone('Asia/Kolkata')->format('H:i'),
                        $t->ferry_type,
                        $t->vehicle_name_join ?? '',
                        $t->vehicle_no_join ?? '',
                        $t->customer_name ?? '',
                        $t->customer_mobile ?? '',
                        number_format((float) $t->total_amount, 2, '.', ''),
                        number_format((float) $t->net_amount, 2, '.', ''),
                    ]);
                }
            }, 'id');

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }


}