<?php
require_once __DIR__ . '/../config/database.php';

class Payment {
    private $conn;
    private $table = 'payment';
    
    public $payment_ID;
    public $payment_type;
    public $payment_time;
    public $payment_date;
    public $amount;
    public $driver_ID;
    public $payment_details;
    
    public function __construct() {
        $this->conn = db_connect();
    }
    
    public function create() {
        if (empty($this->payment_time)) {
            $this->payment_time = date('H:i:s');
        }
        if (empty($this->payment_date)) {
            $this->payment_date = date('Y-m-d');
        }
        
        $payment_type = mysqli_real_escape_string($this->conn, $this->payment_type);
        $payment_time = mysqli_real_escape_string($this->conn, $this->payment_time);
        $payment_date = mysqli_real_escape_string($this->conn, $this->payment_date);
        $amount = mysqli_real_escape_string($this->conn, $this->amount);
        $driver_ID = mysqli_real_escape_string($this->conn, $this->driver_ID);
        $payment_details = mysqli_real_escape_string($this->conn, $this->payment_details);
        
        $query = "INSERT INTO " . $this->table . " (payment_type, payment_time, payment_date, amount, driver_ID, payment_details) VALUES ('$payment_type', '$payment_time', '$payment_date', '$amount', '$driver_ID', '$payment_details')";
        if (mysqli_query($this->conn, $query)) {
            $this->payment_ID = mysqli_insert_id($this->conn);
            return $this->payment_ID;
        }
        return false;
    }
    
    public function getByDriverId($driver_id) {
        $driver_id = mysqli_real_escape_string($this->conn, $driver_id);
        $query = "SELECT * FROM " . $this->table . " WHERE driver_ID = '$driver_id' ORDER BY payment_date DESC, payment_time DESC";
        $result = mysqli_query($this->conn, $query);
        $payments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $payments[] = $row;
        }
        return $payments;
    }
}
?>
