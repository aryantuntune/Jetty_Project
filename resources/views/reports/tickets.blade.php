@extends('layouts.app')

@section('content')
<style>
    .win-card { border: 2px solid #9ec5fe; background: #f8fafc; box-shadow: 2px 2px 6px rgba(0,0,0,0.15); }
    .win-header { background: #fff; text-align: center; font-weight: bold; color: darkred; padding: 6px; border-bottom: 1px solid #ccc; }
    .win-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .win-table th { background: #f0f0f0; border: 1px solid #ccc; padding: 4px; text-align: left; }
    .win-table td { border: 1px solid #ccc; padding: 4px; }
    .win-row { background: #eaffea; }
    .win-row:hover { background: #1e3a8a; color: white; }
    .win-footer { background: darkred; color: white; padding: 6px 10px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
    .btn-add { background: #1d4ed8; color: white; padding: 4px 10px; border-radius: 3px; text-decoration: none; font-size: 13px; }
    .btn-small { font-size: 12px; padding: 4px 8px; }
    .scroll-box { max-height: 450px; overflow-y: auto; }
</style>

<div class="container mx-auto p-4">
    <div class="win-card">
        <!-- Header -->
        <div class="win-header">Ticket Details</div>

        <!-- Filters -->
        <div class="flex gap-2 px-4 py-2">
            <form method="GET" action="{{ route('reports.tickets') }}" class="flex flex-wrap gap-2 w-full">
                <select name="branch_id" id="branch_id" class="border px-2 py-1 text-sm w-1/5">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>

                <select name="payment_mode" class="border px-2 py-1 text-sm w-1/5">
                    <option value="">All Payment Modes</option>
                    @foreach($paymentModes as $mode)
                        <option value="{{ $mode }}" {{ $paymentMode == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                    @endforeach
                </select>

                <select name="ferry_type" class="border px-2 py-1 text-sm w-1/5">
                    <option value="">All Types</option>
                    @foreach($ferryTypes as $type)
                        <option value="{{ $type }}" {{ $ferryType == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>

                <select name="ferry_boat_id" id="ferry_boat_id" class="border px-2 py-1 text-sm w-1/5">
                    <option value="">All Boats</option>
                    @if($branchId)
                        @foreach($ferryBoats->where('branch_id', $branchId) as $boat)
                            <option value="{{ $boat->id }}" {{ $ferryBoatId == $boat->id ? 'selected' : '' }}>
                                {{ $boat->name }}
                            </option>
                        @endforeach
                    @endif
                </select>

                <input type="date" name="date_from" value="{{ $dateFrom }}" class="border px-2 py-1 text-sm w-1/6">
                <input type="date" name="date_to" value="{{ $dateTo }}" class="border px-2 py-1 text-sm w-1/6">

                <button type="submit" class="border px-3 py-1 bg-gray-200">Filter</button>
                <a href="{{ route('reports.tickets') }}" class="border px-3 py-1 bg-gray-200">Reset</a>
                
               
              

            </form>
        </div>

        <!-- Table -->
        <div class="scroll-box">
            <table class="win-table">
                <thead>
                    <tr>
                        <th>Ticket Date</th>
                        <th>Ticket No</th>
                        <th>Pay Mode</th>
                        <th>Boat Name</th>
                        <th>Ferry Time</th>
                        <th>Ferry Type</th>
                        <th>Customer Name</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr class="win-row">
                            <td>{{ $ticket->created_at->format('d-m-Y') }}</td>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->payment_mode }}</td>
                            <td>{{ $ticket->ferryBoat->name ?? '-' }}</td>
                            <td>{{ optional($ticket->ferry_time)->format('H:i') }}</td>
                            <td>{{ $ticket->ferry_type }}</td>
                            <td>{{ $ticket->customer_name ?? '-' }}</td>
                            <td class="text-right">{{ number_format($ticket->total_amount, 2) }}</td>
                             <td>
      <div class="flex gap-1">
        <!-- reusing your existing route + controller -->
        <a target="_blank" rel="noopener"
           class="btn-small border px-2 py-1"
           href="{{ route('tickets.print', ['ticket' => $ticket->id, 'w' => '58']) }}">
           Print 58mm
        </a>
        <a target="_blank" rel="noopener"
           class="btn-small border px-2 py-1"
           href="{{ route('tickets.print', ['ticket' => $ticket->id, 'w' => '80']) }}">
           Print 80mm
        </a>
      </div>
    </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center p-2">No tickets found.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-end">Total:</th>
                        <th class="text-right">{{ number_format($totalAmount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
            <div class="p-3">
    {{ $tickets->appends(request()->query())->links() }}
</div>
        </div>

        <!-- Footer -->
        <div class="win-footer">
            <span>Total Records: {{ $tickets->count() }}</span>
              <a class=" px-3 py-1 bg-green-500 text-white"
                    href="{{ route('reports.tickets.export', request()->query()) }}">
                    Export CSV
                    </a>
        </div>
    </div>
</div>

{{-- Script for dependent dropdown --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#branch_id').on('change', function () {
        var branchId = $(this).val();
        var ferryBoatDropdown = $('#ferry_boat_id');

        ferryBoatDropdown.empty().append('<option value="">All Boats</option>');

        if (branchId) {
            $.ajax({
                url: "{{ url('/branches') }}/" + branchId + "/ferryboats",
                type: "GET",
                success: function (data) {
                    $.each(data, function (key, boat) {
                        ferryBoatDropdown.append(
                            '<option value="'+ boat.id +'">'+ boat.name +'</option>'
                        );
                    });
                }
            });
        }
    });
</script>
@endsection
