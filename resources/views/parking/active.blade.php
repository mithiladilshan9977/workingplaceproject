<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Parking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .active-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            margin: 0 auto;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .card-body {
            padding: 40px;
        }

        .parking-active-badge {
            display: inline-block;
            background: #d4edda;
            color: #155724;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
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
    </style>
</head>
<body>
    <div class="active-card">
        <div class="card-header">
            <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
            <h1>Parking Active</h1>
            <p style="margin: 10px 0 0;">You are currently parked</p>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="text-center mb-4">
                <span class="parking-active-badge">
                    <i class="bi bi-circle-fill me-2"></i>Active Parking
                </span>
            </div>

            <div class="booking-details mb-4">
                <h4 class="mb-3"><i class="bi bi-info-circle me-2"></i>Parking Details</h4>
                
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
                    <span class="detail-label"><i class="bi bi-clock me-2"></i>Check-In Time</span>
                    <span class="detail-value">{{ $booking->checked_in_at->format('h:i A, d M Y') }}</span>
                </div>

                <div class="detail-row" style="background: #f0f8ff;">
                    <span class="detail-label"><i class="bi bi-cash me-2"></i>Base Charge Paid</span>
                    <span class="detail-value" style="color: #27ae60;">
                        LKR {{ number_format($booking->vehicle_type_charge, 2) }}
                    </span>
                </div>
            </div>

            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Hourly Rate:</strong> LKR {{ $currentRate->rate_per_hour ?? 100 }}/hour applies to your parking duration.
            </div>

            <a href="{{ route('parking.checkout', $booking->id) }}" class="btn btn-checkout">
                <i class="bi bi-box-arrow-right me-2"></i>Proceed to Check-Out
            </a>

            <div class="text-center mt-3">
                <a href="{{ route('parking.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-house-fill me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>