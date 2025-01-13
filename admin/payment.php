<?php
session_start();
include('../helper.php');
// Check if the user is logged in and if they are an admin
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    ?>
    <script>window.location.href = "../index.php";</script>
   <?php
    exit();
}
include("../config.php");
// Fetch all users from the database
$sql = "SELECT user_id, full_name FROM users ORDER BY created_at DESC"; // Fetch specific fields
$result = $conn->query($sql);

// Fetch users into an array
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle adding payment details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_payment'])) {
    $user_id = $_POST['user_id'];
    $payment_date = $_POST['payment_date'];
    $repay_date = $_POST['repay_date'];
    $amount = $_POST['amount'];
    $interest = $_POST['interest'];
    $purpose = $_POST['purpose'];
    $due_amount = $_POST['due_amount'];
    $proof = $_POST['proof'];
    $repay_mode = $_POST['repay_mode'];
    $status = $_POST['status'];
    $late_charges = $_POST['late_charges'];
    $late_date = $_POST['late_date'];
    // die();
    $stmt = $conn->prepare("INSERT INTO payments (user_id, payment_date, repay_date, amount, interest, purpose, due_amount, proof, repay_mode, status, late_charges, late_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssddsdssssd", $user_id, $payment_date, $repay_date, $amount, $interest,  $purpose, $due_amount, $proof, $repay_mode, $status, $late_charges, $late_date);
    $stmt->execute();
    $stmt->close();

    ?>
    <script>window.location.href = "payment.php";</script>
    <?php
        // Redirect to the admin panel
    exit();
}

// Check if user_id is provided via GET request
if (isset($_GET['delete_paym_id'])) {
    $delete_paym_id = $_GET['delete_paym_id'];
    // Delete related rows in other tables
    $stmt1 = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
    $stmt1->bind_param("i", $delete_paym_id);
    $stmt1->execute();
    $stmt1->close();
    ?>
    <script>window.location.href = "payment.php";</script>
    <?php // Redirect to login page if not an admin
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../static/css/dashboard.css"> <!-- External CSS File -->
    <style>
        /* General Reset and Basic Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-image: url('../static/image/3.jpg'); /* Replace with actual image path */
            background-size: cover;
            background-position: center;
            color: #333;
            padding: 0 20px;
            min-height: 100vh;
        }

        header {
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        button, .btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration:none;
        }

        .btn:hover, button:hover {
            background-color: #45a049;
        }

        .admin-panel-container {
            padding: 30px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .form-container input, .form-container select {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container label {
            font-weight: bold;
        }
        tbody tr:hover{
            background-color: #ffffff3d;
        }
    </style>
</head>
<body>
    <header>
    <h1 style="color: white;">Admin Panel</h1>
    </header>

    <div class="admin-panel-container">
        <!-- Button to Add Payment Details -->
        <button id="paytablebtn">Add Payment Details</button>
        <a class="btn" style="font-size:13px;" href='index.php'>Dashboard</a>

        <!-- Add Payment Form -->
        <div class="form-container" id="add-payment-form" style="display: none;">
            <h3>Add Payment Details</h3>
            <form method="POST">
                <!-- Dropdown for User ID -->
                <label for="user_id">Select User:</label>
                <select name="user_id" required>
                    <option value="">-- Select User --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['user_id'] ?? '') ?>">
                            <?= htmlspecialchars($user['user_id'] ?? 'Unknown') ?> - <?= htmlspecialchars($user['full_name'] ?? 'Unknown') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!--PLACE REQUIRED NEAR placeholder="Payment Date" required> TO MAKE IT COMPULSORY. SAME FOR REPAY DATE -->
<!--required-->
                <input type="date" name="payment_date" placeholder="Payment Date" > 
                <input type="date" name="repay_date" placeholder="Repay Date">
                <input type="number" step="0.01" name="amount" placeholder="Amount" required>
                <input type="number" step="0.01" name="interest" placeholder="Interest">
                <input type="text" name="purpose" placeholder="Purpose">
                <input type="number" step="0.01" name="due_amount" placeholder="Due Amount">
                <input type="text" name="proof" placeholder="Proof">
                <input type="text" name="repay_mode" placeholder="Repay Mode">
                <input type="text" name="status" placeholder="Status">
                <input type="number" step="0.01" name="late_charges" placeholder="Late Charges">
                <input type="date" name="late_date" placeholder="Late Date">
                <button type="submit" name="add_payment">Add Payment</button>
            </form>
        </div>
    </div>
    <main class="dashboard-container" style="min-height:10vh;">
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>S.NO</th>
          <th>User ID</th>
          <th>PAYMENT DATE</th>
          <th>AMT</th>
          <th>INTEREST</th>
          <th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        <!-- Data will be populated dynamically here -->
         <?php
         // Fetch all payments details from the database
            $sql = "SELECT * FROM `payments`"; // Fetch specific fields
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $sno = 1;
                while ($row = $result->fetch_assoc()) {
                   ?>
                <tr>
                    <td><?php echo $sno;?></td>
                    <td><?php echo $row['user_id'];?></td>
                    <td><?php echo $row['payment_date'];?></td>
                    <td><?php echo $row['amount'];?></td>
                    <td><?php echo $row['interest'];?></td>
                    <td>
                        <button class="actions-btn" onclick="editdetails('<?php echo $row['payment_id']; ?>')">Edit</button>
                        <button class="actions-btn" onclick="viewdetails('<?php echo $row['payment_id']; ?>')">View</button>
                        <button class="actions-btn" onclick="deletedetails('<?php echo $row['payment_id']; ?>')">Delete</button>
                    </td>
                </tr>
                   <?php
                   $sno++;
                }
            }else{
                ?> <tr><td colspan="6">No Data Found</td></tr> <?php
            }
            
?>
         
      </tbody>
    </table>
  </main>
    <script>
        document.querySelector("#paytablebtn").addEventListener("click", function () {
        const form = document.getElementById('add-payment-form');
        if (form.style.display === "none" || form.style.display === "") {
            this.innerText = 'Close Payment Detail';
            form.style.display = 'block';
        } else {
            this.innerText = 'Add Payment Detail';
            form.style.display = 'none';
        }
    });
    function editdetails(payid){
        window.location.href = 'viewpayment.php?edit_paym_id=' + payid;
    }
    function viewdetails(payid){
        window.location.href = 'viewpayment.php?paym_id=' + payid;
    }
    function deletedetails(payid){
        window.location.href = '?delete_paym_id=' + payid;
    }
    </script>
</body>
</html>
<?php $conn->close(); ?>
