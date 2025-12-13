<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'from_branch_id',
        'to_branch_id',
        'booking_date',
        'total_amount',
        'payment_status',
        'payment_id',
        'status'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}