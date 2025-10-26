@component('mail::message')
# Daily Ticket Report ({{ $date }})

Total Amount (all tickets on {{ $date }}): **₹ {{ number_format($totalAmount, 2) }}**

The CSV is attached.

Thanks,  
Suvarna Durga Reports
@endcomponent
