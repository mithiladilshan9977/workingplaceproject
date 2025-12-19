<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .bookings-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            background: white;
            border-radius: 20px 20px 0 0;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .bookings-list {
            background: white;
            border-radius: 0 0 20px 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .booking-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-active { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .booking-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 700;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="bookings-container">
        <div class="page-header">
            <h1><i class="bi bi-list-ul me-2"></i>My Parking Bookings</h1>
            <p class="text-muted mb-0">View and manage your parking history</p>
        </div>

        <div class="bookings-list">
            <div class="mb-4">
                <a href="{{ route('parking.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>New Booking
                </a>
            </div>

            @forelse($bookings as $booking)
                <div class="booking-item">
                    <div class="booking-header">
                        <div style="font-weight: 800; color: #667eea; font-size: 1.2rem;">
                            <i class="bi bi-ticket-perforated me-2"></i>{{ $booking->booking_reference }}
                        </div>
                        <span class="status-badge status-{{ $booking->status }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    <div class="booking-details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-p-square me-1"></i>Slot</span>
                            <span class="detail-value">{{ $booking->parkingSlot->slot_number }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-car-front-fill me-1"></i>Vehicle</span>
                            <span class="detail-value">{{ $booking->vehicleType->name }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-hash me-1"></i>Number</span>
                            <span class="detail-value">{{ $booking->vehicle_number }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-calendar3 me-1"></i>Date</span>
                            <span class="detail-value">{{ $booking->booking_date->format('d M Y') }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-clock me-1"></i>Time</span>
                            <span class="detail-value">
                                {{ date('h:i A', strtotime($booking->entry_time)) }}
                                @if($booking->exit_time)
                                    - {{ date('h:i A', strtotime($booking->exit_time)) }}
                                @endif
                            </span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-currency-dollar me-1"></i>Amount</span>
                            <span class="detail-value" style="color: #27ae60;">
                                LKR {{ number_format($booking->total_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-3">
                        @if($booking->status === 'pending')
                            <a href="{{ route('parking.checkin', $booking->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Check In
                            </a>
                        @elseif($booking->status === 'active')
                            <a href="{{ route('parking.active', $booking->id) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-eye me-1"></i>View Active
                            </a>
                            <a href="{{ route('parking.checkout', $booking->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-box-arrow-right me-1"></i>Check Out
                            </a>
                        @elseif($booking->status === 'completed')
                            <a href="{{ route('parking.receipt', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-receipt me-1"></i>View Receipt
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3 text-muted">No bookings found</h4>
                    <a href="{{ route('parking.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Book Now
                    </a>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</body>
</html>