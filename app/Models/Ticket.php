<?php

// app/Models/Ticket.php
namespace App\Models;

use App\Models\TicketLine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'ticket_number',
        'customer_id',
        'total_amount',
        'status',
        'branch_id',
        'ferry_boat_id',
        'payment_mode',
        'ferry_time',
        'discount_pct',
        'discount_rs',
        'user_id',
        'ferry_type',
        'customer_name',
        'customer_mobile',
        'guest_id',
        'verified_at',
    ];

     protected $dates = [
        'created_at',
        'updated_at',
        'verified_at',
    ];
    
    protected $casts = [
        'ferry_time' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(TicketLine::class);
    }

    public function ferryBoat()
    {
        return $this->belongsTo(FerryBoat::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

       public function user()
    {
        return $this->belongsTo(User::class); // uses user_id
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}