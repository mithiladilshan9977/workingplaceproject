<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .receipt-card {
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

        .success-icon {
            font-size: 5rem;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .card-body {
            padding: 40px;
        }

        .receipt-box {
            border: 2px dashed #667eea;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .total-box {
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .total-amount {
            font-size: 2.5rem;
            color: #27ae60;
            font-weight: 800;
            text-align: center;
        }

        .btn-action {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="card-header">
            <i class="bi bi-check-circle-fill success-icon"></i>
            <h1 class="mt-3">Payment Complete!</h1>
            <p style="margin: 10px 0 0;">Thank you for using our parking service</p>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="receipt-box">
                <h4 class="text-center mb-3">PARKING RECEIPT</h4>
                <p class="text-center text-muted">Booking Reference</p>
                <h3 class="text-center" style="color: #667eea; font-weight: 800;">{{ $booking->booking_reference }}</h3>
            </div>

            <div class="mb-4">
                <h5><i class="bi bi-info-circle me-2"></i>Parking Details</h5>
                
                <div class="detail-row">
                    <span>Parking Slot:</span>
                    <strong>{{ $booking->parkingSlot->slot_number }}</strong>
                </div>

                <div class="detail-row">
                    <span>Vehicle Type:</span>
                    <strong>{{ $booking->vehicleType->name }}</strong>
                </div>

                <div class="detail-row">
                    <span>Vehicle Number:</span>
                    <strong>{{ $booking->vehicle_number }}</strong>
                </div>

                <div class="detail-row">
                    <span>Driver Name:</span>
                    <strong>{{ $booking->driver_name }}</strong>
                </div>

                <div class="detail-row">
                    <span>Phone:</span>
                    <strong>{{ $booking->driver_phone }}</strong>
                </div>

                <div class="detail-row">
                    <span>Date:</span>
                    <strong>{{ $booking->booking_date->format('d M Y') }}</strong>
                </div>

                <div class="detail-row">
                    <span>Entry Time:</span>
                    <strong>{{ date('h:i A', strtotime($booking->entry_time)) }}</strong>
                </div>

                <div class="detail-row">
                    <span>Exit Time:</span>
                    <strong>{{ date('h:i A', strtotime($booking->exit_time)) }}</strong>
                </div>

                <div class="detail-row">
                    <span>Duration:</span>
                    <strong>{{ $booking->duration_hours }} hour(s)</strong>
                </div>
            </div>

            <div class="total-box">
                <h5 class="text-center mb-3"><i class="bi bi-calculator me-2"></i>Payment Breakdown</h5>
                
                <div class="detail-row">
                    <span>Vehicle Type Charge:</span>
                    <strong>LKR {{ number_format($booking->vehicle_type_charge, 2) }}</strong>
                </div>

                <div class="detail-row">
                    <span>Hourly Charge ({{ $booking->duration_hours }} hours):</span>
                    <strong>LKR {{ number_format($booking->hourly_charge, 2) }}</strong>
                </div>

                <hr>

                <div class="total-amount">
                    LKR {{ number_format($booking->total_amount, 2) }}
                </div>
                <p class="text-center text-muted mb-0">Total Amount Paid</p>
            </div>

            <div class="text-center">
                <button onclick="window.print()" class="btn btn-outline-primary btn-action">
                    <i class="bi bi-printer me-2"></i>Print Receipt
                </button>
                
                <a href="{{ route('parking.index') }}" class="btn btn-primary btn-action">
                    <i class="bi bi-house-fill me-2"></i>New Booking
                </a>

                <a href="{{ route('parking.bookings') }}" class="btn btn-outline-secondary btn-action">
                    <i class="bi bi-list-ul me-2"></i>My Bookings
                </a>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted">
                    Checked out at {{ $booking->checked_out_at->format('h:i A, d M Y') }}
                </small>
            </div>
        </div>
    </div>
</body>
</html>