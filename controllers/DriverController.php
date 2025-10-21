<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Vehicle.php';
require_once __DIR__ . '/../models/ParkingRecord.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/Payment.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'driver') {
    header('Location: ../index.php');
    exit();
}

$db = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['add_vehicle'])) {
        $vehicle = new Vehicle($db);
        $vehicle->vehicle_number = $_POST['vehicle_number'];
        $vehicle->vehicle_type = $_POST['vehicle_type'];
        $vehicle->driver_ID = $_SESSION['user_id'];
        
        if ($vehicle->create()) {
            $_SESSION['success'] = 'Vehicle added successfully';
        } else {
            $_SESSION['error'] = 'Failed to add vehicle';
        }
        header('Location: ../views/driver-dashboard.php');
        exit();
    }
    
    if (isset($_POST['delete_vehicle'])) {
        $vehicle = new Vehicle($db);
        if ($vehicle->delete($_POST['vehicle_id'], $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Vehicle deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete vehicle';
        }
        header('Location: ../views/driver-dashboard.php');
        exit();
    }
    
    if (isset($_POST['book_parking'])) {
        $parkingRecord = new ParkingRecord($db);
        $activeBooking = $parkingRecord->getActiveByVehicleId($_POST['vehicle_id']);
        
        if ($activeBooking) {
            $_SESSION['error'] = 'Vehicle already has an active booking';
            header('Location: ../views/driver-dashboard.php');
            exit();
        }
        
        $location = new Location($db);
        $availableSlots = $location->getAvailableSlots($_POST['location_id']);
        
        if ($availableSlots <= 0) {
            $_SESSION['error'] = 'No available slots at this location';
            header('Location: ../views/driver-dashboard.php');
            exit();
        }
        
        // Create parking record first
        $parkingRecord->vehicle_ID = $_POST['vehicle_id'];
        $parkingRecord->location_ID = $_POST['location_id'];
        
        if ($parkingRecord->create()) {
            // Store payment information
            $payment = new Payment($db);
            $payment->payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : 'Cash';
            $payment->driver_ID = $_SESSION['user_id'];
            
            // Store payment details based on payment type
            $paymentDetails = [];
            if (isset($_POST['cardholder_name'])) {
                $paymentDetails['cardholder_name'] = $_POST['cardholder_name'];
            }
            if (isset($_POST['card_number'])) {
                // Mask card number for security (show only last 4 digits)
                $cardNumber = $_POST['card_number'];
                $maskedCard = str_repeat('*', strlen($cardNumber) - 4) . substr($cardNumber, -4);
                $paymentDetails['card_number'] = $maskedCard;
            }
            if (isset($_POST['expiry_date'])) {
                $paymentDetails['expiry_date'] = $_POST['expiry_date'];
            }
            if (isset($_POST['mobile_number'])) {
                $paymentDetails['mobile_number'] = $_POST['mobile_number'];
            }
            
            // Store as JSON in a field or create payment record
            $payment->amount = 0.00; // Will be updated when parking ends
            $payment->payment_details = json_encode($paymentDetails);
            $payment->create();
            
            $_SESSION['success'] = 'Parking booked successfully! Payment details saved.';
        } else {
            $_SESSION['error'] = 'Failed to book parking';
        }
        header('Location: ../views/driver-dashboard.php');
        exit();
    }
    
    if (isset($_POST['end_parking'])) {
        $parkingRecord = new ParkingRecord($db);
        $record = $parkingRecord->getById($_POST['record_id']);
        
        if (!$record) {
            $_SESSION['error'] = 'Parking record not found';
            header('Location: ../views/driver-dashboard.php');
            exit();
        }
        
        $parkingRecord->record_ID = $_POST['record_id'];
        $parkingRecord->start_time = $record['start_time'];
        
        if ($parkingRecord->endParking()) {
            $payment = new Payment($db);
            $payment->payment_type = 'Cash';
            $payment->amount = $parkingRecord->total_fee;
            $payment->driver_ID = $_SESSION['user_id'];
            $payment->create();
            
            $_SESSION['success'] = 'Parking ended. Duration: ' . $parkingRecord->duration . ' hrs. Fee: Rs. ' . $parkingRecord->total_fee;
        } else {
            $_SESSION['error'] = 'Failed to end parking';
        }
        header('Location: ../views/driver-dashboard.php');
        exit();
    }
}
?>
