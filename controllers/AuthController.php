<?php
session_start();
require_once __DIR__ . '/../models/Driver.php';
require_once __DIR__ . '/../models/SpaceProvider.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['driver_register'])) {
        $driver = new Driver();
        $driver->name = $_POST['name'];
        $driver->phone_number = $_POST['phone_number'];
        $driver->email = $_POST['email'];
        $driver->password = $_POST['password'];
        
        if ($driver->emailExists($driver->email)) {
            $_SESSION['error'] = 'Email already registered';
            header('Location: ../index.php');
            exit();
        }
        
        if ($driver->register()) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: ../index.php');
            exit();
        }
    }
    
    if (isset($_POST['driver_login'])) {
        $driver = new Driver();
        $driver->email = $_POST['email'];
        $driver->password = $_POST['password'];
        
        $result = $driver->login();
        
        if ($result) {
            $_SESSION['user_type'] = 'driver';
            $_SESSION['user_id'] = $result['driver_ID'];
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_email'] = $result['email'];
            header('Location: ../views/driver-dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: ../index.php');
            exit();
        }
    }
    
    if (isset($_POST['provider_register'])) {
        $provider = new SpaceProvider();
        $provider->name = $_POST['name'];
        $provider->phone_number = $_POST['phone_number'];
        $provider->email = $_POST['email'];
        $provider->password = $_POST['password'];
        
        if ($provider->emailExists($provider->email)) {
            $_SESSION['error'] = 'Email already registered';
            header('Location: ../index.php');
            exit();
        }
        
        if ($provider->register()) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: ../index.php');
            exit();
        }
    }
    
    if (isset($_POST['provider_login'])) {
        $provider = new SpaceProvider();
        $provider->email = $_POST['email'];
        $provider->password = $_POST['password'];
        
        $result = $provider->login();
        
        if ($result) {
            $_SESSION['user_type'] = 'provider';
            $_SESSION['user_id'] = $result['provider_ID'];
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_email'] = $result['email'];
            header('Location: ../views/provider-dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: ../index.php');
            exit();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: ../index.php');
    exit();
}
?>
