<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'item_short_name',
        'item_name_regional',
        'item_category_id',
        'item_rate',
        'item_lavy',
        'item_surcharge_pct',
        'levy_surcharge_pct',
        'space_units',
        'is_fixed_rate',
        'is_vehicle',
        'is_active',
        'branch_id',
        'starting_date',
        'ending_date',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'starting_date' => 'date',
        'ending_date'   => 'date',
        'item_rate'     => 'decimal:2',
        'item_lavy'     => 'decimal:2',

    ];

    // Relationships (adjust table/model names if yours differ)
    public function category()
    {
        return $this->belongsTo(\App\Models\ItemCategory::class, 'item_category_id');
    }
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Scope for "currently effective" rows
    public function scopeEffective($q, $on = null)
    {
        $on = $on ?: now()->toDateString();
        return $q->where('starting_date', '<=', $on)
            ->where(function ($w) use ($on) {
                $w->whereNull('ending_date')->orWhere('ending_date', '>=', $on);
            });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
