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
    <div class="win-header">Vehicle Wise Tickets Details</div>

    {{-- Filters --}}
    {{-- Filters (single-line) --}}
<div class="px-2 py-3 overflow-x-auto">
  <form method="GET" action="{{ route('reports.vehicle_tickets') }}"
        class="flex items-center gap-2 whitespace-nowrap w-max">

    <select name="branch_id"
            class="border px-2 py-1 text-sm rounded">
      <option value="">All Branches</option>
      @foreach($branches as $b)
        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>
          {{ $b->branch_name }}
        </option>
      @endforeach
    </select>

    <input type="date" name="date_from" value="{{ $dateFrom }}"
           class="border px-2 py-1 text-sm rounded" />
    <span class="text-sm">to</span>
    <input type="date" name="date_to" value="{{ $dateTo }}"
           class="border px-2 py-1 text-sm rounded" />

    <input type="text" name="vehicle_no" placeholder="Vehicle No" value="{{ $vehicleNo }}"
           class="border px-2 py-1 text-sm rounded" />
    <input type="text" name="vehicle_name" placeholder="Vehicle Name" value="{{ $vehicleName }}"
           class="border px-2 py-1 text-sm rounded" />

    <button type="submit"
            class="border px-3 py-1 text-sm bg-gray-200 rounded">
      Filter
    </button>

    <a href="{{ route('reports.vehicle_tickets') }}"
       class="border px-3 py-1 text-sm bg-gray-200 rounded">
      Reset
    </a>

    <a href="{{ route('reports.vehicle_tickets.export', request()->query()) }}"
       class="px-3 py-1 text-sm bg-green-500 text-white rounded">
      Export CSV
    </a>
  </form>
</div>


    {{-- Header strip --}}
    {{-- <div class="px-4 pb-2 text-sm text-gray-600">
      <div>
        <strong>Branch Name:</strong>
        {{ optional($branches->firstWhere('id', $branchId))->branch_name ?? 'All' }}
      </div>
      <div>
        <strong>Date From:</strong> {{ $dateFrom ?: '—' }} &nbsp;&nbsp;
        <strong>To:</strong> {{ $dateTo ?: '—' }}
      </div>
      <div>
        <strong>Vehicle No:</strong> {{ $vehicleNo ?: 'All' }} &nbsp;&nbsp;
        <strong>Vehicle:</strong> {{ $vehicleName ?: 'All' }}
      </div>
    </div> --}}

    {{-- Grid --}}
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
            <th>Vehicle No</th>
            <th>Vehicle Name</th>
            <th class="text-right">Amount</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tickets as $t)
            <tr class="win-row">
              <td>{{ $t->created_at->format('d/m/Y') }}</td>
              <td>{{ $t->id }}</td>
              <td>{{ $t->payment_mode }}</td>
              <td>{{ $t->ferryBoat->name ?? '-' }}</td>
              <td>
                @php
                  $ft = optional($t->ferry_time);
                  echo $ft ? (method_exists($ft,'format') ? $ft->format('H:i:s') : $t->ferry_time) : '-';
                @endphp
              </td>
              <td>{{ $t->ferry_type ?? '-' }}</td>
              <td>{{ $t->vehicle_no_join ?? '-' }}</td>
              <td>{{ $t->vehicle_name_join ?? '-' }}</td>
              <td class="text-right">{{ number_format($t->total_amount, 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center p-3">No tickets found.</td>
            </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr>
            <th colspan="8" class="text-right">Total (this page):</th>
            <th class="text-right">{{ number_format($pageTotalAmount, 2) }}</th>
          </tr>
          {{-- <tr>
            <th colspan="8" class="text-right">Total (all filtered):</th>
            <th class="text-right">{{ number_format($totalAmount, 2) }}</th>
          </tr> --}}
        </tfoot>
      </table>

      <div class="p-3">
        {{ $tickets->appends(request()->query())->links() }}
      </div>
    </div>

    <div class="win-footer">
      <span>Total Records: {{ $tickets->count() }}</span>
      <a class="px-3 py-1 bg-green-500 text-white"
   href="{{ route('reports.vehicle_tickets.export', request()->query()) }}">
   Export CSV
</a>
    </div>
  </div>
</div>
@endsection
