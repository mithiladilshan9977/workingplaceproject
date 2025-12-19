<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_price',
        'icon',
        'is_active'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function parkingSlots()
    {
        return $this->hasMany(ParkingSlot::class);
    }

    public function bookings()
    {
        return $this->hasMany(ParkingBooking::class);
    }

    public static function getActiveTypes()
    {
        return self::where('is_active', true)->orderBy('name')->get();
    }
}