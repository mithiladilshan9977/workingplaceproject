<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_number',
        'status',
        'vehicle_type_id'
    ];

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function bookings()
    {
        return $this->hasMany(ParkingBooking::class);
    }

    public function activeBooking()
    {
        return $this->hasOne(ParkingBooking::class)
            ->where('status', 'active')
            ->latest();
    }

    public static function getAvailableCount($vehicleTypeId = null)
    {
        // Count total available slots
        $totalAvailableSlots = self::where('status', 'available')->count();
        
        // Count active bookings (currently occupying slots)
        $activeBookings = ParkingBooking::where('status', 'active')->count();
        
        // Calculate available slots
        $availableCount = $totalAvailableSlots - $activeBookings;
        
        return max(0, $availableCount);
    }

    public static function getAvailableSlot($vehicleTypeId)
    {
        return self::where('status', 'available')
            ->whereDoesntHave('bookings', function ($query) {
                $query->where('status', 'active');
            })
            ->first();
    }
}