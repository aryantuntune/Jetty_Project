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
        'item_rate_id',
        'quantity',
        'rate',
        'total',
        'item_id',
        'item_name',
        'qty',
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

    public function itemRate()
    {
        return $this->belongsTo(ItemRate::class, 'item_rate_id');
    }
}