<?php
// Database credentials
$servername = "localhost";
$username = "gotmydet_dip";
$password = "agzRE_nLJUXHCH4";
$dbname = "gotmydet_ails"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>