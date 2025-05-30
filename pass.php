<?php
// Assuming you have a database connection already established in 'db.php'
require 'db.php';

// The username and other editor information you want to insert
$username = 'editor'; // Replace with the actual editor username
$email = 'editor@gmail.com'; // Replace with the editor's email
$role = 'editor'; // The role for the user (editor in this case)
$password = '12321'; // Plain text password

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the SQL query to insert the editor into the users table
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

// Execute the query
if ($stmt->execute()) {
    echo "Editor has been successfully added!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
