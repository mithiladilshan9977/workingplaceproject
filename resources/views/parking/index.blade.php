<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Parking Entry - Book Your Slot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }

        .parking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .card-header-custom h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .availability-banner {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .availability-banner.warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .availability-banner.danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        }

        .slots-count {
            font-size: 3rem;
            font-weight: 800;
            margin: 10px 0;
        }

        .card-body-custom {
            padding: 40px;
        }

        .vehicle-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .vehicle-type-card {
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .vehicle-type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .vehicle-type-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
        }

        .vehicle-type-card input[type="radio"] {
            display: none;
        }

        .vehicle-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 10px;
        }

        .vehicle-name {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .vehicle-price {
            color: var(--success-color);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .btn-primary-custom:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-primary-custom:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid var(--secondary-color);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="parking-card">
            <div class="card-header-custom">
                <i class="bi bi-car-front" style="font-size: 4rem;"></i>
                <h1>Parking Entry</h1>
                <p style="margin: 10px 0 0;">Book Your Parking Slot</p>
            </div>

            <div class="availability-banner" id="availabilityBanner">
                <h3 style="margin: 0;">Available Parking Slots</h3>
                <div class="slots-count" id="slotsCount">{{ $availableSlots }}</div>
                <p style="margin: 0;" id="slotsMessage">
                    {{ $availableSlots > 0 ? 'Slots Available' : 'No Slots Available' }}
                </p>
            </div>

            <div class="card-body-custom">
                @if(session('success'))
                    <div class="alert alert-success alert-custom">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-custom">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <div class="info-box">
                    <h5><i class="bi bi-info-circle me-2"></i>Parking Information</h5>
                    <ul style="margin-bottom: 0;">
                        <li>Base charge applies per vehicle type</li>
                        <li>Additional: LKR {{ $currentRate->rate_per_hour ?? 100 }}/hour after entry</li>
                        <li>Total Slots Available: <strong>10</strong></li>
                    </ul>
                </div>

                <form action="{{ route('parking.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-car-front-fill me-2"></i>Select Vehicle Type *
                        </label>
                        <div class="vehicle-type-grid">
                            @foreach($vehicleTypes as $type)
                                <div class="vehicle-type-card" data-type-id="{{ $type->id }}" data-price="{{ $type->base_price }}">
                                    <input type="radio" name="vehicle_type_id" value="{{ $type->id }}" 
                                           id="type_{{ $type->id }}" {{ old('vehicle_type_id') == $type->id ? 'checked' : '' }}>
                                    <label for="type_{{ $type->id }}" style="cursor: pointer; width: 100%;">
                                        <i class="bi {{ $type->icon }} vehicle-icon"></i>
                                        <div class="vehicle-name">{{ $type->name }}</div>
                                        <div class="vehicle-price">LKR {{ number_format($type->base_price, 2) }}</div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('vehicle_type_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="booking_date" class="form-label">
                                <i class="bi bi-calendar3 me-2"></i>Booking Date
                            </label>
                            <input type="date" 
                                   class="form-control @error('booking_date') is-invalid @enderror" 
                                   id="booking_date" 
                                   name="booking_date" 
                                   value="{{ old('booking_date', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('booking_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="entry_time" class="form-label">
                                <i class="bi bi-clock me-2"></i>Entry Time
                            </label>
                            <input type="time" 
                                   class="form-control @error('entry_time') is-invalid @enderror" 
                                   id="entry_time" 
                                   name="entry_time" 
                                   value="{{ old('entry_time', date('H:i')) }}"
                                   required>
                            @error('entry_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_number" class="form-label">
                                <i class="bi bi-hash me-2"></i>Vehicle Number
                            </label>
                            <input type="text" 
                                   class="form-control @error('vehicle_number') is-invalid @enderror" 
                                   id="vehicle_number" 
                                   name="vehicle_number" 
                                   value="{{ old('vehicle_number') }}"
                                   placeholder="e.g., ABC-1234"
                                   required>
                            @error('vehicle_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="driver_name" class="form-label">
                                <i class="bi bi-person-fill me-2"></i>Driver Name
                            </label>
                            <input type="text" 
                                   class="form-control @error('driver_name') is-invalid @enderror" 
                                   id="driver_name" 
                                   name="driver_name" 
                                   value="{{ old('driver_name') }}"
                                   placeholder="Full Name"
                                   required>
                            @error('driver_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="driver_phone" class="form-label">
                            <i class="bi bi-telephone-fill me-2"></i>Phone Number
                        </label>
                        <input type="tel" 
                               class="form-control @error('driver_phone') is-invalid @enderror" 
                               id="driver_phone" 
                               name="driver_phone" 
                               value="{{ old('driver_phone') }}"
                               placeholder="0771234567"
                               required>
                        @error('driver_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info" id="priceInfo" style="display: none;">
                        <h5><i class="bi bi-cash me-2"></i>Base Charge</h5>
                        <p class="mb-0">Vehicle Type Charge: <strong id="basePrice">LKR 0.00</strong></p>
                        <small class="text-muted">Additional LKR 100/hour applies after check-in</small>
                    </div>

                    <button type="submit" class="btn btn-primary-custom" id="submitBtn">
                        <i class="bi bi-check-circle-fill me-2"></i>Reserve Parking Slot
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('parking.bookings') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list-ul me-2"></i>View My Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Vehicle type selection
        document.querySelectorAll('.vehicle-type-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.vehicle-type-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                const price = this.dataset.price;
                document.getElementById('basePrice').textContent = `LKR ${parseFloat(price).toFixed(2)}`;
                document.getElementById('priceInfo').style.display = 'block';
                
                checkAvailability();
            });
        });

        // Pre-select if old value exists
        const selectedTypeId = "{{ old('vehicle_type_id') }}";
        if (selectedTypeId) {
            const card = document.querySelector(`.vehicle-type-card[data-type-id="${selectedTypeId}"]`);
            if (card) {
                card.click();
            }
        }

        // Check availability
        function checkAvailability() {
            const vehicleTypeId = document.querySelector('input[name="vehicle_type_id"]:checked')?.value;
            
            if (!vehicleTypeId) return;

            fetch('{{ route("parking.check-availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    vehicle_type_id: vehicleTypeId
                })
            })
            .then(response => response.json())
            .then(data => {
                const banner = document.getElementById('availabilityBanner');
                const count = document.getElementById('slotsCount');
                const message = document.getElementById('slotsMessage');
                const submitBtn = document.getElementById('submitBtn');
                
                count.textContent = data.available_count;
                
                if (data.available_count > 5) {
                    banner.className = 'availability-banner';
                    message.textContent = 'Plenty of Slots Available';
                    submitBtn.disabled = false;
                } else if (data.available_count > 0) {
                    banner.className = 'availability-banner warning';
                    message.textContent = 'Limited Slots Available';
                    submitBtn.disabled = false;
                } else {
                    banner.className = 'availability-banner danger';
                    message.textContent = 'No Slots Available';
                    submitBtn.disabled = true;
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const vehicleType = document.querySelector('input[name="vehicle_type_id"]:checked');
            
            if (!vehicleType) {
                e.preventDefault();
                alert('Please select a vehicle type');
                return false;
            }

            const availableSlots = parseInt(document.getElementById('slotsCount').textContent);
            
            if (availableSlots <= 0) {
                e.preventDefault();
                alert('No parking slots available. Please try again later.');
                return false;
            }
        });
    </script>
</body>
</html>