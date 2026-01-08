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
        'branch_address',
        'branch_phone',
        'dest_branch_id',
        'dest_branch_name',
        'ferry_boat_id',
        'user_id',
        'is_active',
        'created_by',
        'updated_by',
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
        return $query->where('is_active', true);
    }
}