<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
</head>

<body style="margin:0;background:#f3f4f6;font-family:Arial,sans-serif">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="600"
                    style="background:#fff;margin:30px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.1)">
                    <tr>
                        <td style="background:#0d6efd;color:#fff;padding:20px;text-align:center">
                            <h2>🚢 Jetty Booking Confirmed</h2>
                            <p>Your ticket & QR code are ready</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:25px">

                            <p>Dear <strong>{{ $booking->customer->first_name }}
                                    {{ $booking->customer->last_name }}</strong>,</p>

                            <p>Your booking has been <strong style="color:green">successfully confirmed</strong>.</p>

                            <hr>

                            <h3>📄 Booking Details</h3>
                            <table width="100%">
                                <tr>
                                    <td>Booking ID</td>
                                    <td>{{ $booking->ticket_id }}</td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td>{{ $booking->booking_date }}</td>
                                </tr>
                                <tr>
                                    <td>Time</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->departure_time)->format('g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td>From</td>
                                    <td>{{ $booking->fromBranch->branch_name }}</td>
                                </tr>
                                <tr>
                                    <td>To</td>
                                    <td>{{ $booking->toBranch->branch_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong>₹{{ number_format($booking->total_amount, 2) }}</strong></td>
                                </tr>
                            </table>

                            <hr>

                            <h3>🧾 Items</h3>

                            @php use App\Models\ItemRate; @endphp

                            <table width="100%" border="1" cellspacing="0" cellpadding="6"
                                style="border-collapse:collapse">
                                <tr style="background:#f1f5f9">
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>Levy</th>
                                    <th>Total</th>
                                </tr>

                                @foreach(json_decode($booking->items, true) as $item)
                                    @php $rate = ItemRate::find($item['item_rate_id']); @endphp
                                    <tr>
                                        <td>{{ $rate->item_name ?? 'Item' }}</td>
                                        <td align="center">{{ $item['quantity'] }}</td>
                                        <td align="right">₹{{ $item['rate'] }}</td>
                                        <td align="right">₹{{ $item['lavy'] }}</td>
                                        <td align="right"><strong>₹{{ $item['total'] }}</strong></td>
                                    </tr>
                                @endforeach
                            </table>

                            <hr>



                            <p>PDF ticket is attached with this email.</p>

                            <p>Regards,<br><strong>Jetty Team</strong></p>

                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f8fafc;text-align:center;font-size:12px;padding:10px">
                            © {{ date('Y') }} Jetty Booking Service
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>

</html>