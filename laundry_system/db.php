<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // Default XAMPP server
$username = "root"; // Default MySQL username in XAMPP
$password = ""; // Default is empty in XAMPP
$dbname = "laundry_db"; // Make sure this matches your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
