  @extends('layouts.app')

  @section('content')
  <div class="container mt-4">
@if(session('error'))
  <div class="alert alert-danger">
      {{ session('error') }}
  </div>
@endif

@if(session('success'))
  <div class="alert alert-success">
      {{ session('success') }}
  </div>
@endif

    <h4 class="mb-3"> Ticket Verification</h4>

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

          @if(!$verifiedAt)
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
