<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestCategory extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    public function guests()
    {
        return $this->hasMany(Guest::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 'Y');
    }
}
