<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Jetty Ticket</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .ticket {
            border: 2px dashed #0d6efd;
            padding: 20px;
            border-radius: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            color: #0d6efd;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .col {
            display: table-cell;
            width: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #f1f5f9;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="ticket">

        <!-- HEADER -->
        <div class="header">
            <h2>🚢 JETTY BOOKING TICKET</h2>
            <p><strong>Ticket No:</strong> {{ $booking->ticket_id }}</p>
        </div>

        <!-- CUSTOMER & ROUTE -->
        <div class="row">
            <div class="col">
                <strong>Passenger:</strong><br>
                {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}<br>
                {{ $booking->customer->email }}
            </div>
            <div class="col">
                <strong>Journey:</strong><br>
                {{ $booking->fromBranch->branch_name }} ➝ {{ $booking->toBranch->branch_name }}
            </div>
        </div>

        <!-- DATE & TIME -->
        <div class="row">
            <div class="col">
                <strong>Date:</strong> {{ $booking->booking_date }}
            </div>
            <div class="col">
                <strong>Departure:</strong> {{ \Carbon\Carbon::parse($booking->departure_time)->format('g:i A') }}
            </div>
        </div>

        <!-- ITEMS -->
        <h4>🧾 Items</h4>

        @php use App\Models\ItemRate; @endphp

        <table>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Levy</th>
                <th>Total</th>
            </tr>

            @foreach(json_decode($booking->items, true) as $item)
                @php $rate = ItemRate::find($item['item_rate_id']); @endphp
                <tr>
                    <td>{{ $item['item_name'] ?? ($rate->item_name ?? 'Item') }}</td>
                    <td>{{ $item['quantity'] ?? 1 }}</td>
                    <td>₹{{ $item['rate'] ?? ($rate->item_rate ?? 0) }}</td>
                    <td>₹{{ $item['lavy'] ?? ($rate->item_lavy ?? 0) }}</td>
                    <td><strong>₹{{ $item['total'] ?? (($item['rate'] ?? 0) + ($item['lavy'] ?? 0)) * ($item['quantity'] ?? 1) }}</strong>
                    </td>
                </tr>
            @endforeach
        </table>

        <!-- TOTAL + STATUS -->
        <p><strong>Total Amount:</strong> ₹{{ number_format($booking->total_amount, 2) }}</p>
        <p><strong>Status:</strong> {{ strtoupper($booking->status) }}</p>


        <!-- QR CODE -->


        <div style="text-align:center;margin-top:15px">
            {{-- <img src="{{ $qrPath }}" width="150" alt="QR Code"> --}}
            <img src="{{ storage_path('app/public/' . $qrImage) }}" width="150">

            <p>Scan QR to verify ticket</p>

        </div>





    </div>

    <div class="footer">
        Please carry this ticket during boarding.<br>
        © {{ date('Y') }} Jetty Booking Service
    </div>

</body>

</html>