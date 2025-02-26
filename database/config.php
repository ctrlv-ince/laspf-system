<?php
// Database configuration
$host = 'localhost'; // Database host
$db   = 'goseekr';    // Database name
$user = 'root';       // Database username 
$pass = '';           // Database password 

// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>