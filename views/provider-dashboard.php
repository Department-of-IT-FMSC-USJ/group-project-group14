<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'provider') {
    header('Location: ../index.php');
    exit();
}

require_once '../config/database.php';
require_once '../models/Location.php';
require_once '../models/ParkingRecord.php';

$db = db_connect();
$locationModel = new Location($db);
$parkingModel = new ParkingRecord($db);

$locations = $locationModel->getByProviderId($_SESSION['user_id']);
$records = $parkingModel->getByProviderId($_SESSION['user_id']);

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/dashboard.css">
    <link rel="stylesheet" href="../public/css/minimalist-background.css">
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
            <h1>Space Provider Dashboard</h1>
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
            <!-- Add Location Form -->
            <div class="dashboard-card">
                <h2>Add Parking Location</h2>
                <form action="../controllers/ProviderController.php" method="POST">
                    <input type="text" name="location_name" placeholder="Location Name" required>
                    <input type="number" name="no_of_slot" placeholder="Number of Slots" min="1" required>
                    <input type="number" name="hourly_rate" placeholder="Hourly Rate (Rs.)" min="1" step="0.01" required>
                    <textarea name="address" placeholder="Address" rows="3" required></textarea>
                    <button type="submit" name="add_location">Add Location</button>
                </form>
            </div>

            <!-- My Locations List -->
            <div class="dashboard-card">
                <h2>My Parking Locations</h2>
                <div class="items-list">
                    <?php if (empty($locations)): ?>
                        <p style="color: #7f8c8d; text-align: center; padding: 20px;">No parking locations added yet.</p>
                    <?php else: ?>
                        <?php foreach ($locations as $location): ?>
                            <div class="item">
                                <div style="flex: 1;">
                                    <strong style="font-size: 16px;"><?php echo htmlspecialchars($location['location_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($location['address']); ?></small><br>
                                    <small style="color: #4a90e2; font-weight: 600;">Slots: <?php echo $location['no_of_slot']; ?> | Rate: Rs.<?php echo $location['hourly_rate']; ?>/hr</small>
                                </div>
                                <form action="../controllers/ProviderController.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="location_id" value="<?php echo $location['location_ID']; ?>">
                                    <button type="submit" name="delete_location" class="delete-btn" onclick="return confirm('Delete this location?')">Delete</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Parking Records -->
            <div class="dashboard-card full-width">
                <h2>Parking Records</h2>
                <div class="items-list">
                    <?php if (empty($records)): ?>
                        <p style="color: #7f8c8d; text-align: center; padding: 20px;">No parking records yet.</p>
                    <?php else: ?>
                        <?php foreach ($records as $record): ?>
                            <div class="item">
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 10px;">
                                        <strong style="font-size: 16px;"><?php echo htmlspecialchars($record['vehicle_number']); ?></strong>
                                        <?php if ($record['end_time']): ?>
                                            <span class="status-badge completed">Completed</span>
                                        <?php else: ?>
                                            <span class="status-badge active">Active</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="color: #7f8c8d; font-size: 14px; line-height: 1.8;">
                                        <strong>Location:</strong> <?php echo htmlspecialchars($record['location_name']); ?><br>
                                        <strong>Driver:</strong> <?php echo htmlspecialchars($record['driver_name']); ?><br>
                                        <strong>Start:</strong> <?php echo date('M d, Y h:i A', strtotime($record['start_time'])); ?>
                                        <?php if ($record['end_time']): ?>
                                            <br><strong>End:</strong> <?php echo date('M d, Y h:i A', strtotime($record['end_time'])); ?>
                                            <br><strong>Duration:</strong> <?php echo $record['duration']; ?> hours | <strong>Fee:</strong> Rs.<?php echo $record['total_fee']; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/js/parking-animations.js"></script>
    <script src="../public/js/alerts.js"></script>
</body>
</html>
