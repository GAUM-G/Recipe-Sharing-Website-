<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$database = "getsetrecipe";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
