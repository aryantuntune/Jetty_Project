<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $fillable = ['category_name', 'levy', 'user_id', 'location_id'];
}
