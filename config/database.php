<?php
$db_host = 'localhost';
$db_port = '3306';
$db_user = 'root';
$db_pass = '';
$db_name = 'parking';

function db_connect() {
    global $db_host, $db_port, $db_user, $db_pass, $db_name;
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}
?>
