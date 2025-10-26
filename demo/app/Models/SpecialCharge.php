<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'special_charge',
    ];

    // Optional: relationship if you have a Branch model
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}