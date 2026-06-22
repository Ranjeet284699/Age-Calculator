<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sweet_shop";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session globally across files
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>