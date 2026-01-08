<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseboatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'capacity_adults',
        'capacity_kids',
        'total_rooms',
        'amenities',
        'image_url',
        'gallery_images',
    ];

    protected $casts = [
        'amenities' => 'array',
        'gallery_images' => 'array',
        'price' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(HouseboatBooking::class, 'room_id');
    }
}
