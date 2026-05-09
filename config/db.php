<?php
$host = "localhost";
$user = "root";   // default XAMPP user
$pass = "";       // leave empty unless you set a MySQL password
$dbname = "vms_db"; // database name as per your SQL file

// Use $conn, not $con
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to the DB: " . $conn->connect_error);
}
?>

