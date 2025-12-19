<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SignupController;
use App\Models\User;
use App\Http\Controllers\ParkingController;

Route::get('/', function () {
    return redirect()->route('parking.index');
});

Route::prefix('parking')->name('parking.')->group(function () {
    Route::get('/', [ParkingController::class, 'index'])->name('index');
    Route::post('/check-availability', [ParkingController::class, 'checkAvailability'])->name('check-availability');
    Route::post('/book', [ParkingController::class, 'store'])->name('store');
    
    Route::get('/checkin/{id}', [ParkingController::class, 'showCheckin'])->name('checkin');
    Route::post('/checkin/{id}', [ParkingController::class, 'checkIn'])->name('checkin.process');
    
    Route::get('/active/{id}', [ParkingController::class, 'showActive'])->name('active');
    
    Route::get('/checkout/{id}', [ParkingController::class, 'showCheckout'])->name('checkout');
    Route::post('/calculate-checkout/{id}', [ParkingController::class, 'calculateCheckout'])->name('calculate-checkout');
    Route::post('/checkout/{id}', [ParkingController::class, 'checkOut'])->name('checkout.process');
    
    Route::get('/receipt/{id}', [ParkingController::class, 'showReceipt'])->name('receipt');
    Route::get('/my-bookings', [ParkingController::class, 'myBookings'])->name('bookings');
});



// Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [LoginController::class, 'login']);

// Route::get('/signup', [SignupController::class, 'showSignupForm'])->name('signup');
// Route::post('/signup', [SignupController::class, 'signup']);


// Route::get('/dashboard', function () {
//     $users = User::all(); // Fetch all users from the DB
//     return view('home.dashboard', compact('users'));
// })->middleware('auth')->name('dashboard');
