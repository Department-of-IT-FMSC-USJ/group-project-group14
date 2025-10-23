<?php
session_start();

if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'driver') {
        header('Location: views/driver-dashboard.php');
    } else {
        header('Location: views/provider-dashboard.php');
    }
    exit();
}

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parking System</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/modern-loader.css">
    <link rel="stylesheet" href="public/css/minimalist-background.css">
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
    
    <div class="modern-loader-container" id="loadingSection">
        <div class="modern-loader">
            <div class="pulse-ring"></div>
            <div class="pulse-ring"></div>
            <div class="loader-circle"></div>
            <div class="loading-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <h2 class="loading-text">Finding Parking Space...</h2>
        </div>
    </div>

    <div class="main-container" id="optionsSection" style="display: none;">
        <h1>Welcome to Car Parking System</h1>
        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <div class="buttons">
            <button onclick="showForm('driver')">Driver</button>
            <button onclick="showForm('provider')">Space Provider</button>
        </div>
    </div>

    <div class="form-container" id="driverLogin" style="display: none;">
        <button class="back-btn" onclick="showForm('optionsSection')">
            <span class="back-arrow">←</span> Back
        </button>
        <h2>Driver Login</h2>
        <form action="controllers/AuthController.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="driver_login">Login</button>
        </form>
        <p>New user? <a href="#" onclick="showForm('driverRegister')">Register here</a></p>
    </div>

    <div class="form-container" id="driverRegister" style="display: none;">
        <button class="back-btn" onclick="showForm('driverLogin')">
            <span class="back-arrow">←</span> Back
        </button>
        <h2>Driver Registration</h2>
        <form action="controllers/AuthController.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="tel" name="phone_number" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="driver_register">Register</button>
        </form>
        <p>Already have an account? <a href="#" onclick="showForm('driverLogin')">Login here</a></p>
    </div>

    <div class="form-container" id="providerLogin" style="display: none;">
        <button class="back-btn" onclick="showForm('optionsSection')">
            <span class="back-arrow">←</span> Back
        </button>
        <h2>Space Provider Login</h2>
        <form action="controllers/AuthController.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="provider_login">Login</button>
        </form>
        <p>New provider? <a href="#" onclick="showForm('providerRegister')">Register here</a></p>
    </div>

    <div class="form-container" id="providerRegister" style="display: none;">
        <button class="back-btn" onclick="showForm('providerLogin')">
            <span class="back-arrow">←</span> Back
        </button>
        <h2>Space Provider Registration</h2>
        <form action="controllers/AuthController.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="tel" name="phone_number" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="provider_register">Register</button>
        </form>
        <p>Already have an account? <a href="#" onclick="showForm('providerLogin')">Login here</a></p>
    </div>

    <script src="public/js/script.js"></script>
    <script src="public/js/parking-animations.js"></script>
    <script src="public/js/alerts.js"></script>
</body>
</html>
