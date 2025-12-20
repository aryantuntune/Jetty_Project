<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jetty Ticket</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        .box { border: 1px solid #000; padding: 15px; }
        h2 { text-align: center; }
    </style>
</head>
<body>

<h2>JETTY BOOKING TICKET</h2>

<div class="box">
    <p><strong>Booking ID:</strong> {{ $booking->id }}</p>
    <p><strong>Customer:</strong> {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</p>
    <p><strong>Email:</strong> {{ $booking->customer->email }}</p>

    <p><strong>From:</strong> {{ $booking->fromBranch->branch_name }}</p>
    <p><strong>To:</strong> {{ $booking->toBranch->branch_name }}</p>

    <p><strong>Date:</strong> {{ $booking->booking_date }}</p>
    <p><strong>Departure:</strong> {{ $booking->departure_time }}</p>

    <p><strong>Total Amount:</strong> ₹{{ number_format($booking->total_amount, 2) }}</p>
    <p><strong>Status:</strong> {{ strtoupper($booking->status) }}</p>

    <p><strong>QR Code:</strong> {{ $booking->qr_code }}</p>

    <hr>

    <strong>Items:</strong>
    <ul>
        @foreach(json_decode($booking->items, true) as $item)
            <li>{{ $item['item_name'] ?? '' }} × {{ $item['quantity'] ?? '' }}</li>
        @endforeach
    </ul>
</div>

<p>Carry this ticket during boarding.</p>

</body>
</html>
