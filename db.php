<?php
$host = "localhost";
// $user = "root";  // Local MySQL user
// $pass = "";      // Local MySQL password (empty for null)
// $dbname = "entry";

$user = "fnkjyinw_entry";  // Default MySQL user
$pass = "Oz_}V)v4PFA!";    // Default MySQL password
$dbname = "fnkjyinw_entry";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
