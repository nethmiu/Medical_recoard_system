<?php
// db_connect.php

$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default password 
$dbname = "medical_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Start session on all pages that include this file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>