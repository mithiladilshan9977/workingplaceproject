<?php

namespace App\Http\Controllers;

use App\Models\ParkingSlot;
use App\Models\VehicleType;
use App\Models\ParkingBooking;
use App\Models\ParkingRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingController extends Controller
{
    public function index()
{
    $totalSlots = ParkingSlot::count();
    $availableStatusSlots = ParkingSlot::where('status', 'available')->count();
    $activeBookings = ParkingBooking::where('status', 'active')->count();
    
    // Debug info
    \Log::info('Parking Slots Debug:', [
        'total_slots' => $totalSlots,
        'available_status' => $availableStatusSlots,
        'active_bookings' => $activeBookings,
        'calculated_available' => $availableStatusSlots - $activeBookings
    ]);
    
    $availableSlots = ParkingSlot::getAvailableCount();
    $vehicleTypes = VehicleType::getActiveTypes();
    $currentRate = ParkingRate::getCurrentRate();
    
    return view('parking.index', compact('availableSlots', 'vehicleTypes', 'currentRate'));
}

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id'
        ]);

        $availableSlots = ParkingSlot::getAvailableCount($request->vehicle_type_id);

        return response()->json([
            'success' => true,
            'available_count' => $availableSlots,
            'can_book' => $availableSlots > 0
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vehicle_number' => 'required|string|max:20',
            'driver_name' => 'required|string|max:100',
            'driver_phone' => 'required|string|max:15',
            'booking_date' => 'required|date|after_or_equal:today',
            'entry_time' => 'required|date_format:H:i'
        ]);

        try {
            DB::beginTransaction();

            // Check availability
            $availableSlots = ParkingSlot::getAvailableCount($request->vehicle_type_id);

            if ($availableSlots <= 0) {
                return back()->with('error', 'No parking slots available for the selected vehicle type.');
            }

            // Find available slot
            $slot = ParkingSlot::getAvailableSlot($request->vehicle_type_id);

            if (!$slot) {
                return back()->with('error', 'No parking slots available.');
            }

            // Get vehicle type price
            $vehicleType = VehicleType::findOrFail($request->vehicle_type_id);

            // Create booking
            $booking = new ParkingBooking($request->all());
            $booking->parking_slot_id = $slot->id;
            $booking->status = 'pending';
            $booking->vehicle_type_charge = $vehicleType->base_price;
            $booking->hourly_charge = 0;
            $booking->total_amount = $vehicleType->base_price;
            $booking->save();

            DB::commit();

            return redirect()->route('parking.checkin', $booking->id)
                ->with('success', 'Parking slot reserved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Booking failed: ' . $e->getMessage());
        }
    }

    public function showCheckin($id)
    {
        $booking = ParkingBooking::with(['parkingSlot', 'vehicleType'])->findOrFail($id);
        
        if ($booking->status !== 'pending') {
            return redirect()->route('parking.index')
                ->with('error', 'This booking has already been processed.');
        }

        $currentRate = ParkingRate::getCurrentRate();
        
        return view('parking.checkin', compact('booking', 'currentRate'));
    }

    public function checkIn($id)
    {
        $booking = ParkingBooking::findOrFail($id);
        
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Invalid booking status.');
        }

        $booking->status = 'active';
        $booking->checked_in_at = now();
        $booking->save();

        // Update slot status
        $booking->parkingSlot->status = 'occupied';
        $booking->parkingSlot->vehicle_type_id = $booking->vehicle_type_id;
        $booking->parkingSlot->save();

        return redirect()->route('parking.active', $booking->id)
            ->with('success', 'Checked in successfully!');
    }

    public function showActive($id)
    {
        $booking = ParkingBooking::with(['parkingSlot', 'vehicleType'])->findOrFail($id);
        
        if ($booking->status !== 'active') {
            return redirect()->route('parking.index')
                ->with('error', 'This booking is not active.');
        }

        $currentRate = ParkingRate::getCurrentRate();
        
        return view('parking.active', compact('booking', 'currentRate'));
    }

    public function showCheckout($id)
    {
        $booking = ParkingBooking::with(['parkingSlot', 'vehicleType'])->findOrFail($id);
        
        if ($booking->status !== 'active') {
            return redirect()->route('parking.index')
                ->with('error', 'This booking is not active.');
        }

        $currentRate = ParkingRate::getCurrentRate();
        
        return view('parking.checkout', compact('booking', 'currentRate'));
    }

    public function calculateCheckout(Request $request, $id)
    {
        $request->validate([
            'exit_time' => 'required|date_format:H:i'
        ]);

        $booking = ParkingBooking::with('vehicleType')->findOrFail($id);
        $booking->calculateCheckoutAmount($request->exit_time);

        return response()->json([
            'success' => true,
            'duration_hours' => $booking->duration_hours,
            'vehicle_type_charge' => number_format($booking->vehicle_type_charge, 2),
            'hourly_charge' => number_format($booking->hourly_charge, 2),
            'total_amount' => number_format($booking->total_amount, 2)
        ]);
    }

    public function checkOut(Request $request, $id)
{
    // Only validate that exit_time exists and is in correct format
    $request->validate([
        'exit_time' => 'required|date_format:H:i'
    ], [
        'exit_time.required' => 'Please enter exit time',
        'exit_time.date_format' => 'Invalid time format'
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
            ->with('error', 'Checkout completed. Please create a new booking.');
    }
}

    public function showReceipt($id)
    {
        $booking = ParkingBooking::with(['parkingSlot', 'vehicleType'])->findOrFail($id);
        
        if ($booking->status !== 'completed') {
            return redirect()->route('parking.index');
        }

        return view('parking.receipt', compact('booking'));
    }

    public function myBookings()
    {
        $bookings = ParkingBooking::with(['parkingSlot', 'vehicleType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('parking.bookings', compact('bookings'));
    }
}