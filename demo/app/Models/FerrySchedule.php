<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FerrySchedule extends Model
{
    protected $fillable = ['hour', 'minute', 'branch_id'];

    public function getScheduleTimeAttribute()
    {
        return sprintf('%02d:%02d:00', $this->hour, $this->minute);
    }
     protected $casts = [
        'hour'   => 'integer',
        'minute' => 'integer',
    ];

    public function branch()   { return $this->belongsTo(\App\Models\Branch::class, 'branch_id'); }
    public function user()     { return $this->belongsTo(\App\Models\User::class, 'user_id'); }

}