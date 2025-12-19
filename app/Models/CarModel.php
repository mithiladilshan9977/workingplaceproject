<?php
// app/Models/CarModel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'size_category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function bookings()
    {
        return $this->hasMany(ParkingBooking::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->brand} {$this->model}";
    }

    public static function getActiveModels()
    {
        return self::where('is_active', true)
            ->orderBy('brand')
            ->orderBy('model')
            ->get();
    }
}