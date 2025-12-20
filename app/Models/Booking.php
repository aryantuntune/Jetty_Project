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
        'status',
        'booking_source',
        'verified_at'
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function fromBranch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'from_branch');
    }

    public function toBranch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'to_branch');
    }
}
