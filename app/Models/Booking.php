<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'customer_id',
        'ferry_id',
        'from_branch',
        'to_branch',
        'booking_date',
        'departure_time',
        'items',
        'total_amount',
        'payment_id',
        'booking_source',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the customer who made the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the ferry boat for this booking.
     */
    public function ferry()
    {
        return $this->belongsTo(FerryBoat::class, 'ferry_id');
    }

    /**
     * Get the departing branch.
     */
    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch');
    }

    /**
     * Get the destination branch.
     */
    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch');
    }
}
