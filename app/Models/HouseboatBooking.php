<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseboatBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'check_in',
        'check_out',
        'guests_adults',
        'guests_children',
        'room_count',
        'total_amount',
        'status',
        'booking_reference',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function room()
    {
        return $this->belongsTo(HouseboatRoom::class, 'room_id');
    }
}
