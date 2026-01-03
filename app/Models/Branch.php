<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'branch_name',
        'user_id',
        'is_active',
    ];

    public function ferryboats()
    {
        return $this->hasMany(FerryBoat::class, 'branch_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'branch_id', 'branch_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 'Y');
    }
}