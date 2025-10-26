<?php

// app/Models/TicketLine.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'item_id',
        'item_name',
        'qty',
        'rate',
        'levy',
        'amount',
        'vehicle_name',
        'vehicle_no',
        'user_id',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}