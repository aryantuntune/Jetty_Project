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
        'branch_id',
        'ferry_boat_id',
        'payment_mode',
        'ferry_time',
        'discount_pct',
        'discount_rs',
        'total_amount',
        'user_id',
        'ferry_type',
        'customer_name',
        'customer_mobile',

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
}