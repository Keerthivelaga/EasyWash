<?php
$servername = getenv('MYSQLHOST');
$username   = getenv('MYSQLUSER');
$password   = getenv('MYSQLPASSWORD');
$dbname     = getenv('MYSQLDATABASE');
$port       = getenv('MYSQLPORT');

if (!$servername || !$username || !$password || !$dbname || !$port) {
    die("Missing Railway environment variables");
}

$conn = new mysqli($servername, $username, $password, $dbname, (int)$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>