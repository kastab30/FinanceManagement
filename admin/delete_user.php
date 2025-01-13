<?php
session_start();

// Check if the user is logged in and if they are an admin
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    ?>
    <script>window.location.href = "index.php";</script>
    <?php
    exit();
}

// Database credentials
$servername = "localhost";
$username = "gotmydet_dip";
$password = "agzRE_nLJUXHCH4";
$dbname = "gotmydet_ails"; // Update to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user_id is provided via GET request
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    // Delete related rows in other tables
    $stmt1 = $conn->prepare("DELETE FROM payments WHERE user_id = ?");
    $stmt1->bind_param("s", $user_id);
    $stmt1->execute();
    // echo "<pre>";print_r($stmt1);die();
    // Delete the user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->close();
    // Redirect back to the admin panel
    ?>
    <script>window.location.href = "index.php";</script>
    <?php // Redirect to login page if not an admin

    exit();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
