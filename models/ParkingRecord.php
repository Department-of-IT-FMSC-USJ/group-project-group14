<?php
require_once __DIR__ . '/../config/database.php';

class ParkingRecord {
    private $conn;
    private $table = 'parking_record';
    
    public $record_ID;
    public $start_time;
    public $end_time;
    public $duration;
    public $total_fee;
    public $vehicle_ID;
    public $location_ID;
    
    public function __construct() {
        $this->conn = db_connect();
    }
    
    public function create() {
        if (empty($this->start_time)) {
            $this->start_time = date('H:i:s');
        }
        $start_time = mysqli_real_escape_string($this->conn, $this->start_time);
        $vehicle_ID = mysqli_real_escape_string($this->conn, $this->vehicle_ID);
        $location_ID = mysqli_real_escape_string($this->conn, $this->location_ID);
        
        $query = "INSERT INTO " . $this->table . " (start_time, vehicle_ID, location_ID, duration, total_fee) VALUES ('$start_time', '$vehicle_ID', '$location_ID', 0, 0.00)";
        if (mysqli_query($this->conn, $query)) {
            $this->record_ID = mysqli_insert_id($this->conn);
            return $this->record_ID;
        }
        return false;
    }
    
    public function endParking($hourly_rate = 100.00) {
        $this->end_time = date('H:i:s');
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        $duration_hours = ceil(($end - $start) / 3600);
        $this->duration = $duration_hours;
        $this->total_fee = $duration_hours * $hourly_rate;
        
        $end_time = mysqli_real_escape_string($this->conn, $this->end_time);
        $duration = mysqli_real_escape_string($this->conn, $this->duration);
        $total_fee = mysqli_real_escape_string($this->conn, $this->total_fee);
        $record_ID = mysqli_real_escape_string($this->conn, $this->record_ID);
        
        $query = "UPDATE " . $this->table . " SET end_time = '$end_time', duration = '$duration', total_fee = '$total_fee' WHERE record_ID = '$record_ID'";
        return mysqli_query($this->conn, $query);
    }
    
    public function getById($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT pr.*, v.vehicle_number, v.vehicle_type, l.name as location_name, l.address FROM " . $this->table . " pr LEFT JOIN vehicle v ON pr.vehicle_ID = v.vehicle_ID LEFT JOIN location l ON pr.location_ID = l.location_ID WHERE pr.record_ID = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }
    
    public function getByDriverId($driver_id) {
        $driver_id = mysqli_real_escape_string($this->conn, $driver_id);
        $query = "SELECT pr.*, v.vehicle_number, v.vehicle_type, l.name as location_name, l.address FROM " . $this->table . " pr INNER JOIN vehicle v ON pr.vehicle_ID = v.vehicle_ID INNER JOIN location l ON pr.location_ID = l.location_ID WHERE v.driver_ID = '$driver_id' ORDER BY pr.record_ID DESC";
        $result = mysqli_query($this->conn, $query);
        $records = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
        return $records;
    }
    
    public function getActiveByVehicleId($vehicle_id) {
        $vehicle_id = mysqli_real_escape_string($this->conn, $vehicle_id);
        $query = "SELECT pr.*, l.name as location_name, l.address FROM " . $this->table . " pr LEFT JOIN location l ON pr.location_ID = l.location_ID WHERE pr.vehicle_ID = '$vehicle_id' AND pr.end_time IS NULL ORDER BY pr.record_ID DESC LIMIT 1";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }
    
    public function getByLocationId($location_id) {
        $location_id = mysqli_real_escape_string($this->conn, $location_id);
        $query = "SELECT pr.*, v.vehicle_number, v.vehicle_type FROM " . $this->table . " pr INNER JOIN vehicle v ON pr.vehicle_ID = v.vehicle_ID WHERE pr.location_ID = '$location_id' ORDER BY pr.record_ID DESC";
        $result = mysqli_query($this->conn, $query);
        $records = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
        return $records;
    }
    
    public function getByProviderId($provider_id) {
        $provider_id = mysqli_real_escape_string($this->conn, $provider_id);
        $query = "SELECT pr.*, v.vehicle_number, v.vehicle_type, l.name as location_name, l.address, d.name as driver_name 
                  FROM " . $this->table . " pr 
                  INNER JOIN vehicle v ON pr.vehicle_ID = v.vehicle_ID 
                  INNER JOIN location l ON pr.location_ID = l.location_id 
                  INNER JOIN driver d ON v.driver_ID = d.driver_ID 
                  WHERE l.provider_ID = '$provider_id' 
                  ORDER BY pr.record_ID DESC";
        $result = mysqli_query($this->conn, $query);
        $records = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
        return $records;
    }
}
?>
