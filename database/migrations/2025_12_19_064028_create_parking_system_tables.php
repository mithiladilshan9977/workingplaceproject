<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create vehicle_types table
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->decimal('base_price', 10, 2);
            $table->string('icon', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create parking_slots table
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_number', 10)->unique();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->onDelete('set null');
            $table->timestamps();
        });

        // Create parking_rates table
        Schema::create('parking_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate_per_hour', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create parking_bookings table
        Schema::create('parking_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference', 20)->unique();
            $table->foreignId('parking_slot_id')->constrained('parking_slots')->onDelete('cascade');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->onDelete('cascade');
            $table->string('vehicle_number', 20);
            $table->string('driver_name', 100);
            $table->string('driver_phone', 15);
            $table->date('booking_date');
            $table->time('entry_time');
            $table->time('exit_time')->nullable();
            $table->integer('duration_hours')->default(0);
            $table->decimal('vehicle_type_charge', 10, 2)->default(0);
            $table->decimal('hourly_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_bookings');
        Schema::dropIfExists('parking_rates');
        Schema::dropIfExists('parking_slots');
        Schema::dropIfExists('vehicle_types');
    }
};