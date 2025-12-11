<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'from_branch',
        'to_branch',
        'items',
        'grand_total',
        'payment_id',
        'status'
    ];
}