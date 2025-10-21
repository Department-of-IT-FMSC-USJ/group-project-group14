<?php
require_once __DIR__ . '/../config/database.php';

class SpaceProvider {
    private $conn;
    private $table = 'space_provider';
    
    public $provider_ID;
    public $name;
    public $phone_number;
    public $email;
    public $password;
    
    public function __construct() {
        $this->conn = db_connect();
    }
    
    public function register() {
        $name = mysqli_real_escape_string($this->conn, $this->name);
        $phone_number = mysqli_real_escape_string($this->conn, $this->phone_number);
        $email = mysqli_real_escape_string($this->conn, $this->email);
        $password = mysqli_real_escape_string($this->conn, $this->password);
        
        $query = "INSERT INTO " . $this->table . " (name, phone_number, email, password) VALUES ('$name', '$phone_number', '$email', '$password')";
        if (mysqli_query($this->conn, $query)) {
            $this->provider_ID = mysqli_insert_id($this->conn);
            return $this->provider_ID;
        }
        return false;
    }
    
    public function login() {
        $email = mysqli_real_escape_string($this->conn, $this->email);
        $password = mysqli_real_escape_string($this->conn, $this->password);
        
        $query = "SELECT provider_ID, name, phone_number, email FROM " . $this->table . " WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($this->conn, $query);
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return false;
    }
    
    public function getById($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT provider_ID, name, phone_number, email FROM " . $this->table . " WHERE provider_ID = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }
    
    public function emailExists($email) {
        $email = mysqli_real_escape_string($this->conn, $email);
        $query = "SELECT provider_ID FROM " . $this->table . " WHERE email = '$email'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_num_rows($result) > 0;
    }
}
?>
