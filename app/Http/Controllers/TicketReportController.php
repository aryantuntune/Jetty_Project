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


class TicketReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filter options
        $branches = Branch::all();
        $ferryBoats = FerryBoat::all();
        $paymentModes = ['CASH MEMO', 'CREDIT MEMO', 'GUEST PASS', 'GPay'];
        $ferryTypes = ['REGULAR', 'SPECIAL']; // Replace with your actual ferry types

        // Query filters
        $branchId = $request->query('branch_id');
        $paymentMode = $request->query('payment_mode');
        $ferryType = $request->query('ferry_type');
        $ferryBoatId = $request->query('ferry_boat_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Fetch tickets
        $tickets = Ticket::with('branch', 'ferryBoat')
            ->where('guest_id',null)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
            ->when($ferryType, fn($q) => $q->where('ferry_type', $ferryType))
            ->when($ferryBoatId, fn($q) => $q->where('ferry_boat_id', $ferryBoatId))
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $totalAmount = $tickets->sum('total_amount');

        return view('reports.tickets', compact(
            'tickets',
            'branches',
            'ferryBoats',
            'paymentModes',
            'ferryTypes',
            'branchId',
            'paymentMode',
            'ferryType',
            'ferryBoatId',
            'dateFrom',
            'dateTo',
            'totalAmount'
        ));
    }

    public function getFerryBoatsByBranch($branchId)
    {
        $ferryBoats = FerryBoat::where('branch_id', $branchId)->get();
        return response()->json($ferryBoats);
    }

  // app/Http/Controllers/TicketReportController.php


public function vehicleWiseIndex(Request $request)
{
    $branches     = Branch::orderBy('branch_name')->get();
    $paymentModes = ['CASH MEMO', 'CREDIT MEMO', 'GUEST PASS', 'GPay'];
    $ferryTypes   = ['REGULAR', 'SPECIAL'];

    $branchId     = $request->query('branch_id');
    $paymentMode  = $request->query('payment_mode');
    $ferryType    = $request->query('ferry_type');
    $ferryBoatId  = $request->query('ferry_boat_id');
    $dateFrom     = $request->query('date_from');
    $dateTo       = $request->query('date_to');
    $vehicleName  = trim((string) $request->query('vehicle_name'));
    $vehicleNo    = trim((string) $request->query('vehicle_no'));

    // Base filter over tickets (vehicle filters via whereHas on lines)
    $filter = Ticket::query()
        ->where('guest_id',null)
        ->when($branchId,    fn($q) => $q->where('branch_id', $branchId))
        ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
        ->when($ferryType,   fn($q) => $q->where('ferry_type', $ferryType))
        ->when($ferryBoatId, fn($q) => $q->where('ferry_boat_id', $ferryBoatId))
        ->when($dateFrom,    fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
        ->when($dateTo,      fn($q) => $q->whereDate('created_at', '<=', $dateTo))
        ->when($vehicleNo,   fn($q) => $q->whereHas('lines',  fn($l) => $l->where('vehicle_no',   'like', "%{$vehicleNo}%")))
        ->when($vehicleName, fn($q) => $q->whereHas('lines',  fn($l) => $l->where('vehicle_name', 'like', "%{$vehicleName}%")));

    // Total across ALL filtered tickets (no joins -> safe)
    $totalAmount = (clone $filter)->sum('total_amount');

    // Grid query with subselects for a representative vehicle_no/name (MIN)
    $base = $filter
        ->with(['branch','ferryBoat'])
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
        ->orderBy('tickets.created_at', 'asc');

    $tickets         = $base->paginate(25);
    $pageTotalAmount = $tickets->sum('total_amount');

    return view('reports.vehicle_tickets', compact(
        'tickets','branches','paymentModes','ferryTypes',
        'branchId','paymentMode','ferryType','ferryBoatId',
        'dateFrom','dateTo','vehicleName','vehicleNo',
        'totalAmount','pageTotalAmount'
    ));
}



private function buildTicketsFilter(Request $request)
{
    $branchId    = $request->query('branch_id');
    $paymentMode = $request->query('payment_mode');
    $ferryType   = $request->query('ferry_type');
    $ferryBoatId = $request->query('ferry_boat_id');
    $dateFrom    = $request->query('date_from');
    $dateTo      = $request->query('date_to');

    return Ticket::query()
        ->with(['branch','ferryBoat'])
        ->when($branchId,    fn($q) => $q->where('branch_id', $branchId))
        ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
        ->when($ferryType,   fn($q) => $q->where('ferry_type', $ferryType))
        ->when($ferryBoatId, fn($q) => $q->where('ferry_boat_id', $ferryBoatId))
        ->when($dateFrom,    fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
        ->when($dateTo,      fn($q) => $q->whereDate('created_at', '<=', $dateTo))
        ->orderBy('created_at', 'desc');
}

private function buildVehicleTicketsFilter(Request $request)
{
    $branchId    = $request->query('branch_id');
    $paymentMode = $request->query('payment_mode');
    $ferryType   = $request->query('ferry_type');
    $ferryBoatId = $request->query('ferry_boat_id');
    $dateFrom    = $request->query('date_from');
    $dateTo      = $request->query('date_to');
    $vehicleName = trim((string)$request->query('vehicle_name'));
    $vehicleNo   = trim((string)$request->query('vehicle_no'));

    return Ticket::query()
        ->with(['branch','ferryBoat'])
        ->when($branchId,    fn($q) => $q->where('branch_id', $branchId))
        ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
        ->when($ferryType,   fn($q) => $q->where('ferry_type', $ferryType))
        ->when($ferryBoatId, fn($q) => $q->where('ferry_boat_id', $ferryBoatId))
        ->when($dateFrom,    fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
        ->when($dateTo,      fn($q) => $q->whereDate('created_at', '<=', $dateTo))
        ->when($vehicleNo,   fn($q) => $q->whereHas('lines',  fn($l) => $l->where('vehicle_no',   'like', "%{$vehicleNo}%")))
        ->when($vehicleName, fn($q) => $q->whereHas('lines',  fn($l) => $l->where('vehicle_name', 'like', "%{$vehicleName}%")))
        ->orderBy('created_at', 'asc');
}

/**
 * GET /reports/tickets/export  → CSV export for Ticket Details
 */
public function exportTicketsCsv(Request $request): StreamedResponse
{
    $filename = 'tickets_' . now('Asia/Kolkata')->format('Ymd_His') . '.csv';

    $callback = function () use ($request) {
        $handle = fopen('php://output', 'w');

        // Header row (include customer fields you added)
        fputcsv($handle, [
            'Ticket Date',
            'Ticket ID',
            'Branch',
            'Payment Mode',
            'Boat Name',
            'Ferry Time',
            'Ferry Type',
            'Customer Name',
            'Customer Mobile',
            'Total Amount'
        ]);

        $this->buildTicketsFilter($request)
            ->chunkById(1000, function ($chunk) use ($handle) {
                foreach ($chunk as $t) {
                    fputcsv($handle, [
                        optional($t->created_at)->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        $t->id,
                        $t->branch->branch_name ?? '',
                        $t->payment_mode,
                        $t->ferryBoat->name ?? '',
                        optional($t->ferry_time)->timezone('Asia/Kolkata')->format('Y-m-d H:i'),
                        $t->ferry_type,
                        $t->customer_name ?? '',
                        $t->customer_mobile ?? '',
                        number_format((float)$t->total_amount, 2, '.', ''),
                    ]);
                }
            }, 'id');

        fclose($handle);
    };

    return response()->streamDownload($callback, $filename, [
        'Content-Type'        => 'text/csv',
        'Cache-Control'       => 'no-store, no-cache',
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
            'Ticket ID',
            'Branch',
            'Payment Mode',
            'Boat Name',
            'Ferry Time',
            'Ferry Type',
            'Vehicle Name (any)',
            'Vehicle No (any)',
            'Customer Name',
            'Customer Mobile',
            'Total Amount'
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
                fputcsv($handle, [
                    optional($t->created_at)->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    $t->id,
                    $t->branch->branch_name ?? '',
                    $t->payment_mode,
                    $t->ferryBoat->name ?? '',
                    optional($t->ferry_time)->timezone('Asia/Kolkata')->format('Y-m-d H:i'),
                    $t->ferry_type,
                    $t->vehicle_name_join ?? '',
                    $t->vehicle_no_join ?? '',
                    $t->customer_name ?? '',
                    $t->customer_mobile ?? '',
                    number_format((float)$t->total_amount, 2, '.', ''),
                ]);
            }
        }, 'id');

        fclose($handle);
    };

    return response()->streamDownload($callback, $filename, [
        'Content-Type'        => 'text/csv',
        'Cache-Control'       => 'no-store, no-cache',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ]);
}


}