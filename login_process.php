<?php
session_start();
require 'db.php'; // Assumes you have a separate db connection file

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate input
    if (empty($username) || empty($password) || empty($role)) {
        header("Location: login.php?error=" . urlencode("Please fill in all fields."));
        exit();
    }

    // Prepare the query to check the user based on username and role
    $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verify user
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check if password matches
        if (password_verify($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['user_id']; // Use user_id from the database
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on the role
            if ($role === 'editor') {
                header("Location: editor_dashboard.php");
            } else {
                header("Location: journalist_dashboard.php");
            }
            exit();
        } else {
            header("Location: login.php?error=" . urlencode("Incorrect password."));
            exit();
        }
    } else {
        header("Location: login.php?error=" . urlencode("User not found or role mismatch."));
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
