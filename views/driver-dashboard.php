<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'driver') {
    header('Location: ../index.php');
    exit();
}

require_once '../config/database.php';
require_once '../models/Vehicle.php';
require_once '../models/Location.php';
require_once '../models/ParkingRecord.php';

$db = db_connect();
$vehicleModel = new Vehicle($db);
$locationModel = new Location($db);
$parkingModel = new ParkingRecord($db);

$vehicles = $vehicleModel->getByDriverId($_SESSION['user_id']);
$locations = $locationModel->getAll();
$bookings = $parkingModel->getByDriverId($_SESSION['user_id']);

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/dashboard.css">
    <link rel="stylesheet" href="../public/css/minimalist-background.css">
    <link rel="stylesheet" href="../public/css/payment-modal.css">
</head>
<body>
    <div class="page-background">
        <div class="parking-grid"></div>
        <div class="slot-marker"></div>
        <div class="slot-marker"></div>
        <div class="slot-marker"></div>
        <div class="slot-marker"></div>
        <div class="slot-marker"></div>
        <div class="direction-line"></div>
        <div class="direction-line"></div>
        <div class="direction-line"></div>
        <div class="p-symbol">P</div>
        <div class="p-symbol">P</div>
        <div class="p-symbol">P</div>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Driver Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <a href="../controllers/AuthController.php?action=logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="dashboard-content">
            <!-- Add Vehicle Form -->
            <div class="dashboard-card">
                <h2>Add Vehicle</h2>
                <form action="../controllers/DriverController.php" method="POST">
                    <input type="text" name="vehicle_number" placeholder="Vehicle Number (e.g., ABC-1234)" required>
                    <select name="vehicle_type" required>
                        <option value="">Select Vehicle Type</option>
                        <option value="Car">Car</option>
                        <option value="Van">Van</option>
                        <option value="SUV">SUV</option>
                        <option value="Motorcycle">Motorcycle</option>
                        <option value="Truck">Truck</option>
                    </select>
                    <button type="submit" name="add_vehicle">Add Vehicle</button>
                </form>
            </div>

            <!-- Book Parking Form -->
            <div class="dashboard-card">
                <h2>Book Parking</h2>
                <form id="bookParkingForm" action="../controllers/DriverController.php" method="POST">
                    <select name="vehicle_id" id="vehicleSelect" required>
                        <option value="">Select Vehicle</option>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?php echo $vehicle['vehicle_ID']; ?>" 
                                    data-vehicle-name="<?php echo htmlspecialchars($vehicle['vehicle_number']) . ' - ' . htmlspecialchars($vehicle['vehicle_type']); ?>">
                                <?php echo htmlspecialchars($vehicle['vehicle_number']); ?> - <?php echo htmlspecialchars($vehicle['vehicle_type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="location_id" id="locationSelect" required>
                        <option value="">Select Location</option>
                        <?php foreach ($locations as $location): ?>
                            <option value="<?php echo $location['location_ID']; ?>"
                                    data-location-name="<?php echo htmlspecialchars($location['location_name']); ?>"
                                    data-hourly-rate="<?php echo $location['hourly_rate']; ?>">
                                <?php echo htmlspecialchars($location['location_name']); ?> - Rs.<?php echo $location['hourly_rate']; ?>/hr
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" id="openPaymentBtn" name="book_parking">Proceed to Payment</button>
                </form>
            </div>

            <!-- My Vehicles List -->
            <div class="dashboard-card">
                <h2>My Vehicles</h2>
                <div class="items-list">
                    <?php if (empty($vehicles)): ?>
                        <p style="color: #7f8c8d; text-align: center; padding: 20px;">No vehicles added yet.</p>
                    <?php else: ?>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <div class="item">
                                <div>
                                    <strong><?php echo htmlspecialchars($vehicle['vehicle_number']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($vehicle['vehicle_type']); ?></small>
                                </div>
                                <form action="../controllers/DriverController.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['vehicle_ID']; ?>">
                                    <button type="submit" name="delete_vehicle" class="delete-btn" onclick="return confirm('Delete this vehicle?')">Delete</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- My Bookings List -->
            <div class="dashboard-card">
                <h2>My Bookings</h2>
                <div class="items-list">
                    <?php if (empty($bookings)): ?>
                        <p style="color: #7f8c8d; text-align: center; padding: 20px;">No bookings yet.</p>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <div class="item">
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 10px;">
                                        <strong style="font-size: 16px;"><?php echo htmlspecialchars($booking['vehicle_number']); ?></strong>
                                        <?php if ($booking['end_time']): ?>
                                            <span class="status-badge completed">Completed</span>
                                        <?php else: ?>
                                            <span class="status-badge active">Active</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="color: #7f8c8d; font-size: 14px; line-height: 1.8;">
                                        <strong>Location:</strong> <?php echo htmlspecialchars($booking['location_name']); ?><br>
                                        <strong>Start:</strong> <?php echo date('M d, Y h:i A', strtotime($booking['start_time'])); ?>
                                        <?php if ($booking['end_time']): ?>
                                            <br><strong>End:</strong> <?php echo date('M d, Y h:i A', strtotime($booking['end_time'])); ?>
                                            <br><strong>Duration:</strong> <?php echo $booking['duration']; ?> hours | <strong>Fee:</strong> Rs.<?php echo $booking['total_fee']; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!$booking['end_time']): ?>
                                    <form action="../controllers/DriverController.php" method="POST" style="display:inline; margin-left: 15px;">
                                        <input type="hidden" name="record_id" value="<?php echo $booking['record_ID']; ?>">
                                        <button type="submit" name="end_parking" class="end-btn" onclick="return confirm('End this parking session?')">End Parking</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/js/parking-animations.js"></script>
    <script src="../public/js/alerts.js"></script>
    <script src="../public/js/payment-modal.js"></script>
    <script>
        // Handle payment button click
        document.getElementById('openPaymentBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            const vehicleSelect = document.getElementById('vehicleSelect');
            const locationSelect = document.getElementById('locationSelect');
            
            if (!vehicleSelect.value || !locationSelect.value) {
                alert('Please select both vehicle and location');
                return;
            }
            
            const vehicleOption = vehicleSelect.options[vehicleSelect.selectedIndex];
            const locationOption = locationSelect.options[locationSelect.selectedIndex];
            
            const bookingData = {
                vehicleId: vehicleSelect.value,
                vehicleName: vehicleOption.dataset.vehicleName,
                locationId: locationSelect.value,
                locationName: locationOption.dataset.locationName,
                hourlyRate: locationOption.dataset.hourlyRate
            };
            
            window.paymentModal.open(bookingData);
        });
    </script>
</body>
</html>
