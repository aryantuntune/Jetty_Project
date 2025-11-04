@extends('layouts.app')

@section('content')
<div class="container mt-4">

  <h4 class="mb-3">🎫 Ticket Verification</h4>

  <form method="get" action="{{ route('verify.index') }}" class="mb-4">
    <label>Scan or Enter Ticket Code:</label>
    <input type="text" name="code" class="form-control" placeholder="Scan QR code or type manually" autofocus required>
    <button type="submit" class="btn btn-primary mt-2">Search</button>
  </form>

  @if($ticket)
    <div class="card">
      <div class="card-body">
        <h5>Ticket #{{ $ticket->id }}</h5>
        <p><b>Branch:</b> {{ $ticket->branch->branch_name ?? '-' }}</p>
        <p><b>Date:</b> {{ optional($ticket->created_at)->timezone('Asia/Kolkata')->format('d-m-Y H:i') }}</p>
        <p><b>Total:</b> ₹{{ number_format($ticket->total_amount, 2) }}</p>
        <p><b>Created By:</b> {{ $ticket->user->name ?? '-' }}</p>
        <p><b>Status:</b>
          @if($ticket->verified_at)
            ✅ Verified ({{ $ticket->verified_at->format('d-m-Y H:i') }})
          @else
            ❌ Not Verified
          @endif
        </p>

        @if(!$ticket->verified_at)
          <form method="post" action="{{ route('verify.ticket') }}">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            <button class="btn btn-success">Mark as Verified</button>
          </form>
        @endif
      </div>
    </div>
  @endif

</div>
@endsection
