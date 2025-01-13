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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_payment']) && isset($_GET['edit_paym_id'])) {
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
    $payment_id = htmlspecialchars($_GET['edit_paym_id']);
    // $stmt = $conn->query("UPDATE `payments` SET user_id = $user_id, payment_date = $payment_date, repay_date = $repay_date, amount = $amount, interest = $interest, purpose = $purpose, due_amount = $due_amount, proof = $proof, repay_mode = $repay_mode, `status` = $status, late_charges = $late_charges, late_date =  $late_date WHERE payment_id = $payment_id");
    // echo '<pre>';
    // print_r($stmt);
    // die();
    error_log("Late Date Value: $late_date");
    $stmt = $conn->prepare("UPDATE `payments` SET `user_id` = ?, `payment_date` = ?, `repay_date` = ?, `amount` = ?, `interest` = ?, `purpose` = ?, `due_amount` = ?, `proof` = ?, `repay_mode` = ?, `status`
 = ?, `late_charges` = ?, `late_date` = ? WHERE `payments`.`payment_id` = ?");
    $stmt->bind_param("sssddsdsssssd", $user_id, $payment_date, $repay_date, $amount, $interest, $purpose, $due_amount, $proof, $repay_mode, $status, $late_charges, $late_date,$payment_id);
    $stmt->execute();
    $stmt->close();
    ?>
    <script>window.location.href = "payment.php";</script>
    <?php
    // sssddsdssssdd
    // sssddsdsssssd
        // Redirect to the admin panel
    exit();

}elseif (isset($_GET['paym_id']) or isset($_GET['edit_paym_id'])) { 
    $payment_id = isset($_GET['paym_id']) ? htmlspecialchars($_GET['paym_id']) : htmlspecialchars($_GET['edit_paym_id']);
    $stmt = $conn->prepare("SELECT * FROM `payments` WHERE payment_id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows <= 0) {
        ?>
        <script>window.location.shref = "payment.php";</script>
        <?php // Redirect to login page if not an admin
        exit();
    }else{
        $row = $result->fetch_assoc();
    } 
}else{
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
        .payment-data{
            min-width: 75vw;
            background-color: #ffffff3d;
            color: white;
            display: flex;
            justify-content: center;
            flex-flow: wrap;

        }
        .payment-data .data{
            display: flex;
            justify-content:space-between;
            margin: 0.5rem 0;
            padding: 1rem;
            border: 1px solid red;
            border-top: 1px;
            border-left: 1px;
            min-width: 30vw;
        }
    </style>
</head>
<body>
    <header>
    <h1 style="color: white;">Admin Panel</h1>
    </header>
    <div class="admin-panel-container">
        <a class="btn" style="font-size:13px;" href='payment.php'>Payment Details</a>
        <a class="btn" style="font-size:13px;" href='index.php'>Dashboard</a>
        </div>
    <?php
    if (isset($_GET['edit_paym_id'])){
        ?>
        <!-- Add Payment Form -->
        <div class="form-container" id="edit-payment-form">
            <h3>Edit Payment Details</h3>
            <form method="POST">
                <!-- Dropdown for User ID -->
                <label for="user_id">Select User:</label>
                <select name="user_id" required>
                    <option value="">-- Select User --</option>
                    <?php foreach ($users as $user): ?>
                        <option <?php echo ($user['user_id'] == $row['user_id']) ? "selected" : ""; ?> value="<?= htmlspecialchars($user['user_id'] ?? '') ?>">
                            <?= htmlspecialchars($user['user_id'] ?? 'Unknown') ?> - <?= htmlspecialchars($user['full_name'] ?? 'Unknown') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!--PLACE REQUIRED NEAR placeholder="Payment Date" required> TO MAKE IT COMPULSORY. SAME FOR REPAY DATE -->
                <input type="date" value="<?php echo $row['payment_date'];?>" name="payment_date" placeholder="Payment Date" >
                <input type="date" value="<?php echo $row['repay_date'];?>" name="repay_date" placeholder="Repay Date">
                <input type="number" value="<?php echo $row['amount'];?>" step="0.01" name="amount" placeholder="Amount" required>
                <input type="number" value="<?php echo $row['interest'];?>" step="0.01" name="interest" placeholder="Interest">
                <input type="text" value="<?php echo $row['purpose'];?>" name="purpose" placeholder="Purpose">
                <input type="number" value="<?php echo $row['due_amount'];?>" step="0.01" name="due_amount" placeholder="Due Amount">
                <input type="text" value="<?php echo $row['proof'];?>" name="proof" placeholder="Proof">
                <input type="text" value="<?php echo $row['repay_mode'];?>" name="repay_mode" placeholder="Repay Mode">
                <input type="text" value="<?php echo $row['status'];?>" name="status" placeholder="Status">
                <input type="number" value="<?php echo $row['late_charges'];?>" step="0.01" name="late_charges" placeholder="Late Charges">
                <input type="date" value="<?php echo $row['late_date'];?>" name="late_date" placeholder="Late Date">
                <button type="submit" name="edit_payment">Edit Payment</button>
            </form>
        </div>
        <?php }else{ ?>
    
    <div class="payment-data">
        <div class="data"><p>UserId</p><p>:</p><p><?php echo $row['user_id'];?></p></div>
        <div class="data"><p>Payment Date</p><p>:</p><p><?php echo $row['payment_date'];?></p></div>
        <div class="data"><p>Amount</p><p>:</p><p><?php echo $row['amount'];?></p></div>
        <div class="data"><p>Interest</p><p>:</p><p><?php echo $row['interest'];?></p></div>
        <div class="data"><p>Purpose</p><p>:</p><p><?php echo $row['purpose'];?></p></div>
        <div class="data"><p>Due Amoun</p><p>:</p><p><?php echo $row['due_amount'];?></p></div>
        <div class="data"><p>Proof</p><p>:</p><p><?php if($row['proof'] === NULL){ echo "NO Proof";}else{echo $row['proof'];}?></p></div>
        <div class="data"><p>Repay Date</p><p>:</p><p><?php echo $row['repay_date'];?></p></div>
        <div class="data"><p>Repay Mode</p><p>:</p><p><?php echo $row['repay_mode'];?></p></div>
        <div class="data"><p>Status</p><p>:</p><p><?php echo $row['status'];?></p></div>
        <div class="data"><p>Late Charges</p><p>:</p><p><?php echo $row['late_charges'];?></p></div>
        <div class="data"><p>Late Date</p><p>:</p><p><?php echo $row['late_date'];?></p></div>
    </div>
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
    function edipetails(payid){
        window.location.href = '?edit_paym_id=' + payid;
    }
    function viewdetails(payid){
        window.location.href = 'viewpayment.php?edit_paym_id=' + payid;
    }
    function deletedetails(payid){
        window.location.href = '?edit_paym_id=' + payid;
    }
    <?php } ?>
    </script>
</body>
</html>
<?php $conn->close(); ?>
