<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'branch_id',
        'user_id',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(GuestCategory::class, 'category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'guest_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
