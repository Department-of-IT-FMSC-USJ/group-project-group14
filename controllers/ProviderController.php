<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/ParkingRecord.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'provider') {
    header('Location: ../index.php');
    exit();
}

$db = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['add_location'])) {
        $location = new Location($db);
        $location->location_name = $_POST['location_name'];
        $location->no_of_slot = (int)$_POST['no_of_slot'];
        $location->hourly_rate = (float)$_POST['hourly_rate'];
        $location->address = $_POST['address'];
        $location->provider_ID = $_SESSION['user_id'];
        
        if ($location->create()) {
            $_SESSION['success'] = 'Location added successfully';
        } else {
            $_SESSION['error'] = 'Failed to add location';
        }
        header('Location: ../views/provider-dashboard.php');
        exit();
    }
    
    if (isset($_POST['update_location'])) {
        $location = new Location($db);
        $location->location_ID = $_POST['location_id'];
        $location->location_name = $_POST['location_name'];
        $location->no_of_slot = (int)$_POST['no_of_slot'];
        $location->hourly_rate = (float)$_POST['hourly_rate'];
        $location->address = $_POST['address'];
        
        if ($location->update()) {
            $_SESSION['success'] = 'Location updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update location';
        }
        header('Location: ../views/provider-dashboard.php');
        exit();
    }
    
    if (isset($_POST['delete_location'])) {
        $location = new Location($db);
        
        if ($location->delete($_POST['location_id'], $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Location deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete location';
        }
        header('Location: ../views/provider-dashboard.php');
        exit();
    }
}
?>
