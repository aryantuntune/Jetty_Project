<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif">

    <h2>🚢 Booking Confirmed!</h2>

    <p>Dear {{ $booking->customer->first_name }} {{ $booking->customer->last_name }},</p>

    <p>Your jetty booking has been <strong>successfully confirmed</strong>.</p>

    <hr>

    <h3>📄 Booking Details</h3>

    <p><strong>Booking ID:</strong> {{ $booking->id }}</p>
    <p><strong>Booking Date:</strong> {{ $booking->booking_date }}</p>
    <p><strong>Departure Time:</strong> {{ $booking->departure_time }}</p>
    <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>

    <p><strong>From Branch:</strong> {{ $booking->fromBranch->branch_name ?? 'N/A' }}</p>
    <p><strong>To Branch:</strong> {{ $booking->toBranch->branch_name ?? 'N/A' }}</p>

    <p><strong>Total Amount:</strong> ₹{{ number_format($booking->total_amount, 2) }}</p>

    <p><strong>QR Code:</strong> {{ $booking->qr_code }}</p>

    <hr>

    <h3>🧾 Items</h3>
    <ul>
        @foreach(json_decode($booking->items, true) as $item)
            <li>
                {{ $item['item_name'] ?? '' }} —
                Qty: {{ $item['quantity'] ?? '' }}
            </li>
        @endforeach
    </ul>

    <hr>

    <p>Please carry this confirmation and QR code while boarding.</p>

    <p>Thank you for choosing <strong>Jetty Booking Service</strong>.</p>

    <br>

    <p>Regards,<br>
    <strong>Jetty Team</strong></p>

</body>
</html>
