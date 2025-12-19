<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-Out - Calculate Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .checkout-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            margin: 0 auto;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .card-header.completed {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .card-body {
            padding: 40px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
        }

        .detail-value {
            font-weight: 700;
            color: #2c3e50;
        }

        .calculation-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .calculation-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }

        .total-row {
            border-top: 2px solid #667eea;
            padding-top: 15px;
            margin-top: 15px;
        }

        .total-amount {
            font-size: 2rem;
            color: #27ae60;
            font-weight: 800;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            margin-top: 20px;
        }

        .already-completed-box {
            background: #d4edda;
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
        }

        .already-completed-box i {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .already-completed-box h3 {
            color: #155724;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .already-completed-box p {
            color: #155724;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="checkout-card">
        @if($booking->status === 'completed')
            <!-- Already Checked Out View -->
            <div class="card-header completed">
                <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                <h1>Already Checked Out</h1>
                <p style="margin: 10px 0 0;">This booking has been completed</p>
            </div>

            <div class="card-body">
                <div class="already-completed-box">
                    <i class="bi bi-check-circle-fill"></i>
                    <h3>Checkout Already Completed</h3>
                    <p>This parking session was completed on {{ $booking->checked_out_at->format('d M Y \a\t h:i A') }}</p>
                    
                    <div class="text-center">
                        <a href="{{ route('parking.receipt', $booking->id) }}" class="btn btn-success btn-lg">
                            <i class="bi bi-receipt me-2"></i>View Receipt
                        </a>
                        <a href="{{ route('parking.index') }}" class="btn btn-outline-primary btn-lg ms-2">
                            <i class="bi bi-house-fill me-2"></i>New Booking
                        </a>
                    </div>
                </div>

                <div class="booking-details mb-4">
                    <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Completed Session Details</h5>
                    
                    <div class="detail-row">
                        <span class="detail-label">Booking Reference</span>
                        <span class="detail-value">{{ $booking->booking_reference }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-p-square me-2"></i>Parking Slot</span>
                        <span class="detail-value">{{ $booking->parkingSlot->slot_number }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-car-front-fill me-2"></i>Vehicle</span>
                        <span class="detail-value">{{ $booking->vehicleType->name }} - {{ $booking->vehicle_number }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-clock me-2"></i>Duration</span>
                        <span class="detail-value">
                            {{ date('h:i A', strtotime($booking->entry_time)) }} - {{ date('h:i A', strtotime($booking->exit_time)) }}
                            ({{ $booking->duration_hours }} hour(s))
                        </span>
                    </div>

                    <div class="detail-row" style="background: #f0f8ff;">
                        <span class="detail-label"><i class="bi bi-cash-stack me-2"></i>Total Paid</span>
                        <span class="detail-value" style="color: #27ae60; font-size: 1.3rem;">
                            LKR {{ number_format($booking->total_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <!-- Normal Checkout View -->
            <div class="card-header">
                <i class="bi bi-box-arrow-right" style="font-size: 4rem;"></i>
                <h1>Check-Out</h1>
                <p style="margin: 10px 0 0;">Complete your parking session</p>
            </div>

            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                    </div>
                @endif

                <div class="booking-details mb-4">
                    <h4 class="mb-3"><i class="bi bi-info-circle me-2"></i>Parking Session</h4>
                    
                    <div class="detail-row">
                        <span class="detail-label">Booking Reference</span>
                        <span class="detail-value">{{ $booking->booking_reference }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-p-square me-2"></i>Parking Slot</span>
                        <span class="detail-value">{{ $booking->parkingSlot->slot_number }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-car-front-fill me-2"></i>Vehicle</span>
                        <span class="detail-value">{{ $booking->vehicleType->name }} - {{ $booking->vehicle_number }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-clock me-2"></i>Entry Time</span>
                        <span class="detail-value">{{ date('h:i A', strtotime($booking->entry_time)) }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-calendar3 me-2"></i>Entry Date</span>
                        <span class="detail-value">{{ $booking->booking_date->format('d M Y') }}</span>
                    </div>
                </div>

                <form action="{{ route('parking.checkout.process', $booking->id) }}" method="POST" id="checkoutForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="exit_time" class="form-label">
                            <i class="bi bi-clock-fill me-2"></i>Exit Time *
                        </label>
                        <input type="time" 
                               class="form-control @error('exit_time') is-invalid @enderror" 
                               id="exit_time" 
                               name="exit_time" 
                               value="{{ old('exit_time', date('H:i')) }}"
                               required>
                        @error('exit_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Entry time was <strong>{{ date('h:i A', strtotime($booking->entry_time)) }}</strong>
                        </small>
                    </div>

                    <div class="calculation-box" id="calculationBox" style="display: none;">
                        <h5><i class="bi bi-calculator me-2"></i>Payment Calculation</h5>
                        
                        <div class="calculation-row">
                            <span>Entry Time:</span>
                            <strong id="entryTimeDisplay">{{ date('h:i A', strtotime($booking->entry_time)) }}</strong>
                        </div>

                        <div class="calculation-row">
                            <span>Exit Time:</span>
                            <strong id="exitTimeDisplay">-</strong>
                        </div>

                        <div class="calculation-row" style="background: #e3f2fd; padding: 10px; margin: 10px -10px; border-radius: 5px;">
                            <span style="font-weight: 700; color: #1565c0;">
                                <i class="bi bi-hourglass-split me-1"></i>Total Duration:
                            </span>
                            <strong id="durationDisplay" style="color: #1565c0; font-size: 1.2rem;">-</strong>
                        </div>

                        <hr>

                        <div class="calculation-row">
                            <span>Vehicle Type Charge ({{ $booking->vehicleType->name }}):</span>
                            <strong style="color: #667eea;">LKR {{ number_format($booking->vehicle_type_charge, 2) }}</strong>
                        </div>

                        <div class="calculation-row">
                            <span>Hourly Charge (<span id="hoursCount">0</span> hour(s) × LKR {{ $currentRate->rate_per_hour ?? 100 }}/hour):</span>
                            <strong id="hourlyCharge" style="color: #667eea;">LKR 0.00</strong>
                        </div>

                        <div class="calculation-row total-row">
                            <span style="font-size: 1.3rem; font-weight: 700;">
                                <i class="bi bi-cash-stack me-1"></i>Total Amount:
                            </span>
                            <span class="total-amount" id="totalAmount">LKR 0.00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-checkout" id="submitBtn" disabled>
                        <i class="bi bi-cash-coin me-2"></i>Complete Check-Out & Pay
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('parking.active', $booking->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if($booking->status !== 'completed')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const bookingId = {{ $booking->id }};
        const entryTime = "{{ $booking->entry_time }}";
        const ratePerHour = {{ $currentRate->rate_per_hour ?? 100 }};
        const vehicleTypeCharge = {{ $booking->vehicle_type_charge }};

        function formatTime12Hour(timeString) {
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const hour12 = hour % 12 || 12;
            return `${hour12}:${minutes} ${ampm}`;
        }

        function calculateHoursBetween(entryTimeStr, exitTimeStr) {
            const [entryHours, entryMinutes] = entryTimeStr.split(':').map(Number);
            const [exitHours, exitMinutes] = exitTimeStr.split(':').map(Number);
            
            const entryTotalMinutes = entryHours * 60 + entryMinutes;
            const exitTotalMinutes = exitHours * 60 + exitMinutes;
            
            // If exit is before entry, assume next day
            let diffMinutes;
            if (exitTotalMinutes < entryTotalMinutes) {
                diffMinutes = (24 * 60 - entryTotalMinutes) + exitTotalMinutes;
            } else {
                diffMinutes = exitTotalMinutes - entryTotalMinutes;
            }
            
            // Convert to hours (round up, minimum 1 hour)
            const hours = Math.max(1, Math.ceil(diffMinutes / 60));
            
            return hours;
        }

        function calculateCheckoutLocal() {
            const exitTime = document.getElementById('exit_time').value;
            
            if (!exitTime) {
                document.getElementById('calculationBox').style.display = 'none';
                document.getElementById('submitBtn').disabled = true;
                return;
            }

            // Calculate duration
            const hours = calculateHoursBetween(entryTime, exitTime);
            
            // Calculate charges
            const hourlyCharge = hours * ratePerHour;
            const totalAmount = vehicleTypeCharge + hourlyCharge;

            // Update display
            document.getElementById('calculationBox').style.display = 'block';
            document.getElementById('exitTimeDisplay').textContent = formatTime12Hour(exitTime);
            document.getElementById('durationDisplay').textContent = `${hours} hour(s)`;
            document.getElementById('hoursCount').textContent = hours;
            document.getElementById('hourlyCharge').textContent = `LKR ${hourlyCharge.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('totalAmount').textContent = `LKR ${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('submitBtn').disabled = false;

            // Also fetch from server
            calculateCheckoutServer(exitTime);
        }

        function calculateCheckoutServer(exitTime) {
            fetch(`/parking/calculate-checkout/${bookingId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    exit_time: exitTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('durationDisplay').textContent = `${data.duration_hours} hour(s)`;
                    document.getElementById('hoursCount').textContent = data.duration_hours;
                    document.getElementById('hourlyCharge').textContent = `LKR ${data.hourly_charge}`;
                    document.getElementById('totalAmount').textContent = `LKR ${data.total_amount}`;
                }
            })
            .catch(error => {
                console.error('Server Error:', error);
            });
        }

        // Event listeners
        document.getElementById('exit_time').addEventListener('change', calculateCheckoutLocal);
        document.getElementById('exit_time').addEventListener('input', calculateCheckoutLocal);

        // Auto-calculate on page load
        window.addEventListener('load', function() {
            const exitTime = document.getElementById('exit_time').value;
            if (exitTime) {
                calculateCheckoutLocal();
            }
        });
    </script>
    @endif
</body>
</html>