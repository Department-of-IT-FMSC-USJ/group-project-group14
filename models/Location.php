<?php
require_once __DIR__ . '/../config/database.php';

class Location {
    private $conn;
    private $table = 'location';
    
    public $location_ID;
    public $location_name;
    public $no_of_slot;
    public $hourly_rate;
    public $address;
    public $provider_ID;
    
    public function __construct() {
        $this->conn = db_connect();
    }
    
    public function create() {
        $location_name = mysqli_real_escape_string($this->conn, $this->location_name);
        $no_of_slot = mysqli_real_escape_string($this->conn, $this->no_of_slot);
        $hourly_rate = $this->hourly_rate ? mysqli_real_escape_string($this->conn, $this->hourly_rate) : 100.00;
        $address = mysqli_real_escape_string($this->conn, $this->address);
        $provider_ID = mysqli_real_escape_string($this->conn, $this->provider_ID);
        
        $query = "INSERT INTO " . $this->table . " (name, no_of_slot, hourly_rate, address, provider_ID) VALUES ('$location_name', '$no_of_slot', '$hourly_rate', '$address', '$provider_ID')";
        if (mysqli_query($this->conn, $query)) {
            $this->location_ID = mysqli_insert_id($this->conn);
            return $this->location_ID;
        }
        return false;
    }
    
    public function getById($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT l.*, sp.name as provider_name FROM " . $this->table . " l LEFT JOIN space_provider sp ON l.provider_ID = sp.provider_ID WHERE l.location_ID = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }
    
    public function getAll() {
        $query = "SELECT l.location_ID, l.name as location_name, l.no_of_slot, COALESCE(l.hourly_rate, 100.00) as hourly_rate, l.address, l.provider_ID, sp.name as provider_name FROM " . $this->table . " l LEFT JOIN space_provider sp ON l.provider_ID = sp.provider_ID ORDER BY l.name";
        $result = mysqli_query($this->conn, $query);
        $locations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $locations[] = $row;
        }
        return $locations;
    }
    
    public function getByProviderId($provider_id) {
        $provider_id = mysqli_real_escape_string($this->conn, $provider_id);
        $query = "SELECT location_ID, name as location_name, no_of_slot, COALESCE(hourly_rate, 100.00) as hourly_rate, address, provider_ID FROM " . $this->table . " WHERE provider_ID = '$provider_id'";
        $result = mysqli_query($this->conn, $query);
        $locations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $locations[] = $row;
        }
        return $locations;
    }
    
    public function update() {
        $location_name = mysqli_real_escape_string($this->conn, $this->location_name);
        $no_of_slot = mysqli_real_escape_string($this->conn, $this->no_of_slot);
        $hourly_rate = $this->hourly_rate ? mysqli_real_escape_string($this->conn, $this->hourly_rate) : 100.00;
        $address = mysqli_real_escape_string($this->conn, $this->address);
        $location_ID = mysqli_real_escape_string($this->conn, $this->location_ID);
        
        $query = "UPDATE " . $this->table . " SET name = '$location_name', no_of_slot = '$no_of_slot', hourly_rate = '$hourly_rate', address = '$address' WHERE location_ID = '$location_ID'";
        return mysqli_query($this->conn, $query);
    }
    
    public function delete($location_id, $provider_id) {
        $location_id = mysqli_real_escape_string($this->conn, $location_id);
        $provider_id = mysqli_real_escape_string($this->conn, $provider_id);
        $query = "DELETE FROM " . $this->table . " WHERE location_ID = '$location_id' AND provider_ID = '$provider_id'";
        return mysqli_query($this->conn, $query);
    }
    
    public function getAvailableSlots($location_id) {
        $location_id = mysqli_real_escape_string($this->conn, $location_id);
        
        $query1 = "SELECT no_of_slot FROM " . $this->table . " WHERE location_ID = '$location_id'";
        $result1 = mysqli_query($this->conn, $query1);
        $total_slots = mysqli_fetch_assoc($result1)['no_of_slot'];
        
        $query2 = "SELECT COUNT(*) as occupied FROM parking_record WHERE location_ID = '$location_id' AND end_time IS NULL";
        $result2 = mysqli_query($this->conn, $query2);
        $occupied_slots = mysqli_fetch_assoc($result2)['occupied'];
        
        return $total_slots - $occupied_slots;
    }
}
?>
