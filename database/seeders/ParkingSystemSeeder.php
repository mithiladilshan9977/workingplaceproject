<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingSlot;
use App\Models\VehicleType;
use App\Models\ParkingRate;
use Illuminate\Support\Facades\DB;

class ParkingSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('parking_bookings')->delete();
        DB::table('parking_slots')->delete();
        DB::table('vehicle_types')->delete();
        DB::table('parking_rates')->delete();

        echo "🧹 Cleared existing data\n";

        // Create vehicle types with base prices
        $vehicleTypes = [
            ['name' => 'Bike', 'base_price' => 50.00, 'icon' => 'bi-bicycle'],
            ['name' => 'Scooter', 'base_price' => 75.00, 'icon' => 'bi-scooter'],
            ['name' => 'Car', 'base_price' => 150.00, 'icon' => 'bi-car-front'],
            ['name' => 'SUV', 'base_price' => 200.00, 'icon' => 'bi-truck'],
            ['name' => 'Van', 'base_price' => 250.00, 'icon' => 'bi-truck-front'],
            ['name' => 'Bus', 'base_price' => 500.00, 'icon' => 'bi-bus-front'],
        ];

        foreach ($vehicleTypes as $type) {
            VehicleType::create($type);
        }

        echo "✅ Created " . count($vehicleTypes) . " vehicle types\n";

        // Create 10 parking slots
        for ($i = 1; $i <= 10; $i++) {
            ParkingSlot::create([
                'slot_number' => 'P-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'available'
            ]);
        }

        echo "✅ Created 10 parking slots\n";

        // Create parking rate
        ParkingRate::create([
            'rate_per_hour' => 100.00,
            'is_active' => true
        ]);

        echo "✅ Set parking rate: LKR 100/hour\n";
        echo "\n🎉 Parking system seeded successfully!\n";
        echo "\n📋 Vehicle Types & Base Prices:\n";
        foreach ($vehicleTypes as $type) {
            echo "   - {$type['name']}: LKR {$type['base_price']}\n";
        }
        echo "\n💰 Hourly Rate: LKR 100/hour (applies after check-in)\n";
    }



    public function showCheckout($id)
{
    $booking = ParkingBooking::with(['parkingSlot', 'vehicleType'])->findOrFail($id);
    
    // Check if already completed
    if ($booking->status === 'completed') {
        return redirect()->route('parking.receipt', $booking->id)
            ->with('info', 'This booking has already been checked out.');
    }
    
    // Check if not active
    if ($booking->status !== 'active') {
        return redirect()->route('parking.index')
            ->with('error', 'This booking is not active and cannot be checked out.');
    }

    $currentRate = ParkingRate::getCurrentRate();
    
    return view('parking.checkout', compact('booking', 'currentRate'));
}

public function checkOut(Request $request, $id)
{
    $request->validate([
        'exit_time' => 'required|date_format:H:i'
    ]);

    $booking = ParkingBooking::findOrFail($id);
    
    // Check if already completed
    if ($booking->status === 'completed') {
        return redirect()->route('parking.index')
            ->with('info', 'This booking has already been checked out.');
    }
    
    if ($booking->status !== 'active') {
        return redirect()->route('parking.index')
            ->with('error', 'Invalid booking status.');
    }

    try {
        $booking->calculateCheckoutAmount($request->exit_time);
        $booking->status = 'completed';
        $booking->checked_out_at = now();
        $booking->save();

        // Update slot status
        $booking->parkingSlot->status = 'available';
        $booking->parkingSlot->vehicle_type_id = null;
        $booking->parkingSlot->save();

        // Redirect to parking index with success message
        return redirect()->route('parking.index')
            ->with('success', 'Successfully checked out! Total amount paid: LKR ' . number_format($booking->total_amount, 2));
    } catch (\Exception $e) {
        return redirect()->route('parking.index')
            ->with('error', 'Checkout failed. Please try again.');
    }
}

}