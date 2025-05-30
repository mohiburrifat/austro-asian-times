<?php
$servername = "localhost";
$username = "root"; // Adjust if necessary
$password = ""; // Your password (default for XAMPP is an empty string)
$dbname = "austro_asian_times"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
?>
