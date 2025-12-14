<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'ferry_id',
        'from_branch',
        'to_branch',
        'booking_date',
        'departure_time',
        'items',
        'total_amount',
        'payment_id',
        'qr_code',
        'status'
    ];
}
