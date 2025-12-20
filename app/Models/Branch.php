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
        'branch_id'
    ];

     public function ferryboats()
    {
        // change 'branch_id' if your FK on ferryboats table is named differently
        return $this->hasMany(\App\Models\FerryBoat::class, 'branch_id');
    }
}