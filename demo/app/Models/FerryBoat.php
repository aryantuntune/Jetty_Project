<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FerryBoat extends Model
{
    protected $table = 'ferryboats';
    protected $fillable = ['number', 'name', 'user_id','branch_id'];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }
}