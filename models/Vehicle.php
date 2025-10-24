<?php
require_once __DIR__ . '/../config/database.php';

class Vehicle {
    private $conn;
    private $table = 'vehicle';
    
    public $vehicle_ID;
    public $vehicle_number;
    public $vehicle_type;
    public $driver_ID;
    
    public function __construct() {
        $this->conn = db_connect();
    }
    
    public function create() {
        $vehicle_number = mysqli_real_escape_string($this->conn, $this->vehicle_number);
        $vehicle_type = mysqli_real_escape_string($this->conn, $this->vehicle_type);
        $driver_ID = mysqli_real_escape_string($this->conn, $this->driver_ID);
        
        $query = "INSERT INTO " . $this->table . " (vehicle_number, vehicle_type, driver_ID) VALUES ('$vehicle_number', '$vehicle_type', '$driver_ID')";
        if (mysqli_query($this->conn, $query)) {
            $this->vehicle_ID = mysqli_insert_id($this->conn);
            return $this->vehicle_ID;
        }
        return false;
    }
    
    public function getById($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT * FROM " . $this->table . " WHERE vehicle_ID = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }
    
    public function getByDriverId($driver_id) {
        $driver_id = mysqli_real_escape_string($this->conn, $driver_id);
        $query = "SELECT * FROM " . $this->table . " WHERE driver_ID = '$driver_id'";
        $result = mysqli_query($this->conn, $query);
        $vehicles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $vehicles[] = $row;
        }
        return $vehicles;
    }
    
    public function delete($vehicle_id, $driver_id) {
        $vehicle_id = mysqli_real_escape_string($this->conn, $vehicle_id);
        $driver_id = mysqli_real_escape_string($this->conn, $driver_id);
        $query = "DELETE FROM " . $this->table . " WHERE vehicle_ID = '$vehicle_id' AND driver_ID = '$driver_id'";
        return mysqli_query($this->conn, $query);
    }
}
?>
