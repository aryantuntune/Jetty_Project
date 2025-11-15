@extends('layouts.app')

@section('content')
<div class="container mt-4">

  <h4 class="mb-3">🎫 Ticket Verification</h4>

  {{-- Search Form --}}
  <form method="get" action="{{ route('verify.index') }}" class="mb-4">
    <label>Scan or Enter Ticket Code:</label>
    <input type="text" name="code" class="form-control" placeholder="Scan QR code or type manually" autofocus required>
    <button type="submit" class="btn btn-primary mt-2">Search</button>
  </form>

  @if($ticket)

  @php
      $createdAt = optional($ticket->created_at)->timezone('Asia/Kolkata');
      $verifiedAt = optional($ticket->verified_at)->timezone('Asia/Kolkata');
  @endphp

  <div class="card">
    <div class="card-body">

      <h5 class="mb-2">Ticket #{{ $ticket->id }}</h5>

      <p><b>Branch:</b> {{ $ticket->branch->branch_name ?? '-' }}</p>
      <p><b>Date:</b> {{ $createdAt->format('d-m-Y H:i') }}</p>
      <p><b>Created By:</b> {{ $ticket->user->name ?? '-' }}</p>
      <p><b>Status:</b>
        @if($verifiedAt)
          <span class="text-success">
              ✅ Verified ({{ $verifiedAt->format('d-m-Y H:i') }})
          </span>
        @else
          <span class="text-danger">❌ Not Verified</span>
        @endif
      </p>

      <hr>

      {{-- Ticket Items --}}
      <h6><b>Items:</b></h6>

      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>Description</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Levy</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($ticket->lines as $ln)
          <tr>
            <td>
              {{ $ln->item_name }}
              @if($ln->vehicle_no || $ln->vehicle_name)
                <br>
                <small class="text-muted">
                  {{ $ln->vehicle_name }} {{ $ln->vehicle_no }}
                </small>
              @endif
            </td>
            <td>{{ $ln->qty }}</td>
            <td>{{ $ln->rate }}</td>
            <td>{{ $ln->levy }}</td>
            <td>{{ $ln->amount }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <p class="mt-2"><b>Total Amount:</b> ₹{{ number_format($ticket->total_amount, 2) }}</p>

      <hr>

      {{-- Verify Button --}}
      @if(!$verifiedAt)
      <form method="post" action="{{ route('verify.ticket') }}">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
        <button class="btn btn-success w-100">Mark as Verified</button>
      </form>
      @endif

    </div>
  </div>

  @endif

</div>
@endsection
