<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FerryBoat extends Model
{
    protected $table = 'ferryboats';

    protected $fillable = [
        'number',
        'name',
        'user_id',
        'branch_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'ferry_boat_id');
    }

    public function schedules()
    {
        return $this->hasMany(FerrySchedule::class, 'ferry_boat_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}