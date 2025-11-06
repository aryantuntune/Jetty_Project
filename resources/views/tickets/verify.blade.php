@extends('layouts.app')

@section('content')
<div class="container mt-4">

  <h4 class="mb-3">🎫 Ticket Verification</h4>

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

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

        @php
          $createdAt = $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->timezone('Asia/Kolkata') : null;
          $verifiedAt = $ticket->verified_at ? \Carbon\Carbon::parse($ticket->verified_at)->timezone('Asia/Kolkata') : null;
        @endphp

        <p><b>Date:</b> {{ $createdAt ? $createdAt->format('d-m-Y H:i') : '-' }}</p>
        <p><b>Total:</b> ₹{{ number_format($ticket->total_amount, 2) }}</p>
        <p><b>Created By:</b> {{ $ticket->user->name ?? '-' }}</p>
        <p><b>Status:</b>
          @if($verifiedAt)
            ✅ Verified ({{ $verifiedAt->format('d-m-Y H:i') }})
          @else
            ❌ Not Verified
          @endif
        </p>

        @if(!$verifiedAt)
          <form method="post" action="{{ route('verify.ticket') }}">
            @csrf
            <!-- send encrypted id instead of plain id -->
            <input type="hidden" name="ticket_id" value="{{ encrypt($ticket->id) }}">
            <button class="btn btn-success">Mark as Verified</button>
          </form>
        @endif
      </div>
    </div>
  @endif

</div>
@endsection
    