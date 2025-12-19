<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .confirmation-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            margin: 0 auto;
            overflow: hidden;
        }

        .success-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
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
            font-size: 2rem;
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

        .btn-action {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            margin: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-active {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="success-header">
            <i class="bi bi-check-circle-fill success-icon"></i>
            <h1 class="mt-3">Booking Confirmed!</h1>
            <p>Your parking slot has been successfully reserved</p>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="booking-ref">
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Booking Reference</p>
                <h2>{{ $booking->booking_reference }}</h2>
                <span class="status-badge status-{{ $booking->status }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>

            <div class="booking-details">
                <h4 class="mb-3"><i class="bi bi-info-circle me-2"></i>Booking Details</h4>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-p-square me-2"></i>Parking Slot</span>
                    <span class="detail-value">{{ $booking->parkingSlot->slot_number }}</span>
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
                    <span class="detail-label"><i class="bi bi-clock-fill me-2"></i>Exit Time</span>
                    <span class="detail-value">{{ date('h:i A', strtotime($booking->exit_time)) }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-hourglass-split me-2"></i>Duration</span>
                    <span class="detail-value">{{ $booking->duration_hours }} hour(s)</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-car-front-fill me-2"></i>Vehicle</span>
                    <span class="detail-value">{{ $booking->carModel->full_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-hash me-2"></i>Car Number</span>
                    <span class="detail-value">{{ $booking->car_number }}</span>
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
                    <span class="detail-label"><i class="bi bi-currency-dollar me-2"></i>Total Amount</span>
                    <span class="detail-value" style="color: #27ae60; font-size: 1.3rem;">
                        LKR {{ number_format($booking->total_amount, 2) }}
                    </span>
                </div>
            </div>

            <div class="text-center mt-4">
                @if($booking->status === 'pending')
                    <form action="{{ route('parking.check-in', $booking->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-action">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Check In Now
                        </button>
                    </form>
                @endif

                @if($booking->status === 'active')
                    <form action="{{ route('parking.check-out', $booking->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-action">
                            <i class="bi bi-box-arrow-right me-2"></i>Check Out
                        </button>
                    </form>
                @endif

                <a href="{{ route('parking.index') }}" class="btn btn-primary btn-action">
                    <i class="bi bi-house-fill me-2"></i>Back to Home
                </a>

                <button onclick="window.print()" class="btn btn-outline-secondary btn-action">
                    <i class="bi bi-printer me-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</body>
</html>