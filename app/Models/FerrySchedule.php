<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FerrySchedule extends Model
{
    protected $fillable = [
        'hour',
        'minute',
        'schedule_time',
        'branch_id',
        'ferry_boat_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hour' => 'integer',
        'minute' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getScheduleTimeAttribute()
    {
        return sprintf('%02d:%02d:00', $this->hour, $this->minute);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function ferryBoat()
    {
        return $this->belongsTo(FerryBoat::class, 'ferry_boat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}