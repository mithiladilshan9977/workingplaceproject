<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In - Confirm Your Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .checkin-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            margin: 0 auto;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .card-header h1 {
            margin: 0;
            font-size: 2rem;
        }

        .card-body {
            padding: 40px;
        }

        .booking-ref {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            border: 2px dashed #667eea;
        }

        .booking-ref h2 {
            color: #667eea;
            font-weight: 800;
            margin: 10px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
        }

        .detail-value {
            font-weight: 700;
            color: #2c3e50;
        }

        .btn-checkin {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            margin-top: 20px;
        }

        .btn-checkin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 172, 254, 0.4);
        }

        .info-badge {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="checkin-card">
        <div class="card-header">
            <i class="bi bi-box-arrow-in-right" style="font-size: 4rem;"></i>
            <h1>Check-In Confirmation</h1>
            <p style="margin: 10px 0 0;">Review your booking details</p>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="booking-ref">
                <p style="margin: 0; color: #666;">Booking Reference</p>
                <h2>{{ $booking->booking_reference }}</h2>
                <span class="info-badge">Pending Check-In</span>
            </div>

            <div class="booking-details">
                <h4 class="mb-3"><i class="bi bi-info-circle me-2"></i>Booking Details</h4>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-p-square me-2"></i>Parking Slot</span>
                    <span class="detail-value">{{ $booking->parkingSlot->slot_number }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-car-front-fill me-2"></i>Vehicle Type</span>
                    <span class="detail-value">{{ $booking->vehicleType->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-hash me-2"></i>Vehicle Number</span>
                    <span class="detail-value">{{ $booking->vehicle_number }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-calendar3 me-2"></i>Date</span>
                    <span class="detail-value">{{ $booking->booking_date->format('d M Y') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-clock me-2"></i>Entry Time</span>
                    <span class="detail-value">{{ date('h:i A', strtotime($booking->entry_time)) }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-person-fill me-2"></i>Driver Name</span>
                    <span class="detail-value">{{ $booking->driver_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-telephone-fill me-2"></i>Phone</span>
                    <span class="detail-value">{{ $booking->driver_phone }}</span>
                </div>

                <div class="detail-row" style="background: #f0f8ff;">
                    <span class="detail-label"><i class="bi bi-cash me-2"></i>Base Charge</span>
                    <span class="detail-value" style="color: #27ae60; font-size: 1.3rem;">
                        LKR {{ number_format($booking->vehicle_type_charge, 2) }}
                    </span>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Note:</strong> Additional charges of LKR {{ $currentRate->rate_per_hour ?? 100 }}/hour will apply based on your parking duration.
            </div>

            <form action="{{ route('parking.checkin.process', $booking->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-checkin">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Confirm Check-In
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('parking.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Cancel
                </a>
            </div>
        </div>
    </div>
</body>
</html>