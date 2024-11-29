<?php
$servername = "localhost"; // Update with your server details
$username = "root"; // Update with your DB username
$password = ""; // Update with your DB password
$dbname = "users"; // Update with your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
