<?php
session_start();

// Check if the user is logged in (i.e., session exists)
if (!isset($_SESSION['user_id'])) {
    // If no session exists, redirect to login page
   ?>
   <script>
    window.location.href = "../index.php";
   </script>
   <?php
   die();
}

// Fetch user information from session (name, email, user_id)
$user_id = $_SESSION['user_id']; // Assume user_id is stored in session

include('config.php');

// Query to fetch user payment details
$sql = "SELECT * FROM payments WHERE user_id = '$user_id'";
$result = $conn->query($sql);

// Fetch payments
$payments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="static/css/dashboard.css">
  <script>
    // Pass the payment data from PHP to JavaScript
    const mockData = <?php echo json_encode($payments); ?>;
    
    // Function to display payment data
    function fetchPayments() {
      const tbody = document.querySelector('.dashboard-table tbody');
      tbody.innerHTML = ''; // Clear existing rows

      // Loop through payment data and create rows
      mockData.forEach((payment, index) => {
        const row = `
          <tr>
            <td>${index + 1}</td>
            <td>${payment.payment_date}</td>
            <td>${payment.amount}</td>
            <td>${payment.interest}</td>
            <td>${payment.purpose}</td>
            <td>${payment.due_amount}</td>
            <td>${payment.proof}</td>
            <td>${payment.repay_date}</td>
            <td>${payment.repay_mode}</td>
            <td>${payment.status}</td>
            
            <td>${payment.late_charges}</td>
            <td>${payment.late_date}</td>
          </tr>
        `;
        tbody.innerHTML += row;
      });
    }
    
    // <td><button class="pay-now-btn">Pay Now</button></td> 
    // PASTE THIS ON LINE 64 WHEN NEEDED

    // Call the fetchPayments function to populate the table on page load
    document.addEventListener('DOMContentLoaded', () => {
      fetchPayments();
    });

    // Toggle visibility of user info and logout options when clicking the avatar
    function toggleUserOptions() {
      const dropdown = document.querySelector('.dropdown-content');
      dropdown.classList.toggle('show');
    }

    // Logout function
    function logout() {
      window.location.href = 'logout.php'; // Redirect to logout.php to handle session destroy
    }
  </script>
  <style>
    /* General Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
    }

    /* Meta viewport */
    @media (max-width: 768px) {
      body {
        font-size: 14px; /* Make text smaller on mobile */
      }
    }

    /* Header */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      background-color: #333;
      color: white;
    }

    .avatar-container {
      position: relative;
      display: flex;
      align-items: center;
      cursor: pointer;
    }

    .avatar {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      margin-right: 10px;
    }

    /* Container for the dropdown that shows user info and logout button */
    .dropdown-content {
      display: none;
      position: absolute;
      top: 50px;
      right: 0;
      background-color: #fff;
      color: #333;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      width: 200px;
      z-index: 1;
    }

    .dropdown-content p {
      margin: 5px 0;
    }

    .dropdown-content button {
      background-color: #f44336;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }

    .dropdown-content button:hover {
      background-color: #e53935;
    }

    /* Show dropdown content when the 'show' class is added */
    .dropdown-content.show {
      display: block;
    }

    /* Dashboard Table */
    .dashboard-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }

    .dashboard-table th, .dashboard-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    .dashboard-table th {
      background-color: #f2f2f2;
    }

    /* Make the table scrollable on mobile */
    @media (max-width: 768px) {
      .dashboard-table {
        overflow-x: auto;
        display: block;
        width: 100%;
      }

      .dashboard-table th, .dashboard-table td {
        white-space: nowrap;
      }

      .dashboard-table td {
        font-size: 12px; /* Adjust text size for better fit */
      }
    }

/* Ensure date columns appear in one line */
.dashboard-table th.pay-date,
.dashboard-table td.pay-date,
.dashboard-table th.repay-date,
.dashboard-table td.repay-date,
.dashboard-table th.late-date,
.dashboard-table td.late-date {
  white-space: nowrap; /* Prevent text wrapping */
}

  </style>
</head>
<body>

  <header class="header">
  <h1 style="color: white;">Dashboard</h1>
    

    <!-- Avatar and Dropdown Info Section -->
    <div class="avatar-container" onclick="toggleUserOptions()">
      <img src="static/image/avatar.jpg" alt="User Avatar" class="avatar">

      <!-- Dropdown content that appears on click -->
      <div class="dropdown-content">
        <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
        <button onclick="logout()">Logout</button>
      </div>
    </div>
  </header>
  
  <main class="dashboard-container">
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>S.NO</th>
          <th class="pay-date">PAYMENT DATE</th>
          <th>AMT</th>
          <th>INTEREST</th>
          <th>PURPOSE</th>
          <th>DUE AMT</th>
          <th>PROOF</th>
          <th class="repay-date"> RE-PAY DATE</th>
          <th>RE-PAY MODE</th>
          <th>STATUS</th>
          <!--<th>PAY-NOW</th>-->
          <th>LATE CHARGES</th>
          <th class="late-date">LATE DATE</th>
        </tr>
      </thead>
      <tbody>
        <!-- Data will be populated dynamically here -->
      </tbody>
    </table>
  </main>
</body>
</html>
