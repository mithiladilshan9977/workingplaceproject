<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate_per_hour',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rate_per_hour' => 'decimal:2'
    ];

    public static function getCurrentRate()
    {
        return self::where('is_active', true)->first();
    }
}