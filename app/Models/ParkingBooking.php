<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ParkingBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'parking_slot_id',
        'vehicle_type_id',
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'booking_date',
        'entry_time',
        'exit_time',
        'duration_hours',
        'vehicle_type_charge',
        'hourly_charge',
        'total_amount',
        'status',
        'checked_in_at',
        'checked_out_at'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'vehicle_type_charge' => 'decimal:2',
        'hourly_charge' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = self::generateBookingReference();
            }
        });
    }

    public function parkingSlot()
    {
        return $this->belongsTo(ParkingSlot::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public static function generateBookingReference()
    {
        do {
            $reference = 'PKG-' . strtoupper(Str::random(8));
        } while (self::where('booking_reference', $reference)->exists());

        return $reference;
    }

    public function calculateCheckoutAmount($exitTime)
{
    // Parse times as strings in HH:MM format
    list($entryHours, $entryMinutes) = explode(':', $this->entry_time);
    list($exitHours, $exitMinutes) = explode(':', $exitTime);
    
    // Convert to total minutes from midnight
    $entryTotalMinutes = ((int)$entryHours * 60) + (int)$entryMinutes;
    $exitTotalMinutes = ((int)$exitHours * 60) + (int)$exitMinutes;
    
    // Calculate difference
    $diffMinutes = 0;
    if ($exitTotalMinutes < $entryTotalMinutes) {
        // Next day scenario
        $diffMinutes = (24 * 60 - $entryTotalMinutes) + $exitTotalMinutes;
    } else {
        // Same day
        $diffMinutes = $exitTotalMinutes - $entryTotalMinutes;
    }
    
    // Convert to hours (round up - minimum 1 hour)
    $hours = max(1, (int) ceil($diffMinutes / 60));
    $this->duration_hours = $hours;
    
    $rate = ParkingRate::where('is_active', true)->first();
    $ratePerHour = $rate ? $rate->rate_per_hour : 100;
    
    $this->hourly_charge = $this->duration_hours * $ratePerHour;
    $this->total_amount = $this->vehicle_type_charge + $this->hourly_charge;
    $this->exit_time = $exitTime;
}
}