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
        // Legacy composite key reference
        'branch_id',
        'ticket_date',
        'ticket_no',
        // Item details
        'item_id',
        'item_name',
        'qty',
        'rate',
        'levy',
        'surcharge_pct',
        'levy_surcharge_pct',
        'amount',
        // Vehicle info
        'vehicle_name',
        'vehicle_no',
        'vehicle_id',
        // Unit tracking
        'unit_no',
        'unit_sr_no',
        // Audit
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ticket_date' => 'date',
        'qty' => 'decimal:2',
        'rate' => 'decimal:2',
        'levy' => 'decimal:2',
        'surcharge_pct' => 'decimal:2',
        'levy_surcharge_pct' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function item()
    {
        return $this->belongsTo(ItemRate::class, 'item_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}