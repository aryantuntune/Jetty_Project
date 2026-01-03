<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $fillable = [
        'category_name',
        'levy',
        'user_id',
        'location_id',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(ItemRate::class, 'item_category_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'item_category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 'Y');
    }
}
