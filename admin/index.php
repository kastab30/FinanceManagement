<?php
session_start();

// Check if the user is logged in and if they are an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin'])) {
    ?>
    <script>window.location.href = "../index.php";</script>
   <?php
    exit();
}
include("../config.php");
// Fetch all users from the database
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

// Fetch users into an array
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle adding new user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    // Fetching the data from the form
    $user_id = trim($_POST['user_id']); // Ensure it's an integer
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $admin = 0;
    // Debugging: Log data to check if correct values are being passed
    error_log("adding user with user_id: " . $user_id);
    error_log("Full Name: " . $full_name);
    error_log("Email: " . $email);
    error_log("Admin: " . $admin);
    $stmt = $conn->prepare("INSERT INTO `users` (`user_id`, `full_name`, `email`, `admin`) VALUES (?, ?, ?, '0')");
    $stmt->bind_param("sss",$user_id, $full_name, $email);
    if ($stmt->execute()) {
        error_log("adding successful for user_id: " . $user_id);
    } else {
        error_log("adding failed for user_id: " . $user_id);
    }
    $stmt->close();
    ?>
    <script>window.location.href = "index.php";</script>
   <?php
   exit();
}

// Handle updating user details after form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    // Fetch and sanitize data
    $user_id = trim($_POST['user_id']); // Ensure it's an integer
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $admin = isset($_POST['admin']) ? 1 : 0;
    // Debugging logs
    error_log("Updating user_id: $user_id, full_name: $full_name, email: $email, admin: $admin");
    // Check if user_id is valid
    if (!empty($user_id)) {
        // Update user details in the database
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("sss", $full_name, $email, $user_id);
//         echo '<pre>';
// echo print_r($stmt);
        if ($stmt->execute()) {
            error_log("Update successful for user_id: $user_id");
        } else {
            error_log("Update failed: " . $stmt->error);
        }
        $stmt->close();
        // Redirect to avoid form resubmission
        ?>
        <script>window.location.href = "index.php";</script>
       <?php
        exit();
    } else {
        error_log("Invalid user_id: $user_id");
    }
}



// Handle editing user
if (isset($_GET['edit_user_id'])) {
    $user_id = $_GET['edit_user_id'];
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $user_result = $conn->query($sql);
    $user = $user_result->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../static/css/dashboard.css"> <!-- External CSS File Link -->
    <style>
        /* General Reset and Basic Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-image: url('../static/image/3.jpg'); /* Replace 'your-image.jpg' with the actual image path */
            background-size: cover;
            background-position: center;
            color: #333;
            padding: 0 20px;
            min-height: 100vh;
        }

        /* Header Styling */
        header {
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Admin Panel Container with Less Visibility */
        .admin-panel-container {
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px); /* Slight blur effect for the background */
            margin-top: 20px;
        }

        /* Button Styling */
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Flex container for the buttons */
        .button-container {
            display: flex;             /* Enable Flexbox layout */
            gap: 10px;                 /* Add some space between the buttons */
            margin-top: 20px;          /* Space from the content above */
        }

        /* Style for the buttons */
        .button-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            flex: 1;                   /* Make buttons take equal space */
        }

        .button-container button:hover {
            background-color: #45a049;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Form Styling */
        .form-container {
            background-color: rgba(255, 255, 255, 0.9); /* Slightly more opaque white background */
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Table Styling */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .user-table th, .user-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .user-table th {
            background-color: #4CAF50;
            color: white;
        }

        /* Table Data Styling for better visibility */
        .user-table td {
            background-color: rgba(255, 255, 255, 0.8); /* Light background for table rows */
            color: #333; /* Dark text for better contrast */
        }

        .user-table tr:hover td {
            background-color: rgba(76, 175, 80, 0.2); /* Highlight row on hover */
            color:white;
        }

        /* Action Buttons */
        .actions-btn {
            padding: 8px 16px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .actions-btn:hover {
            background-color: #c0392b;
        }

        /* Avatar Section */
        .avatar-container {
            float: right;
            cursor: pointer;
            position: relative;
        }

        .avatar-container img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        /* Dropdown Content */
        .dropdown-content {
            display: none; /* Hidden by default */
            position: absolute;
            background-color: white; /* Solid background for clarity */
            right: 0;
            top: 100%;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for better visibility */
            min-width: 150px;
            padding: 10px;
            border-radius: 5px;
            z-index: 10; /* Ensures it appears above other elements */
        }

        .dropdown-content p {
            margin: 5px 0;
            font-size: 14px;
            color: #333;
        }

        .dropdown-content button {
            padding: 10px 15px;
            width: 100%;
            text-align: left;
            border: none;
            background: none;
            cursor: pointer;
            color: #333;
            font-size: 14px;
        }

        .dropdown-content button:hover {
            background-color: #f1f1f1; /* Subtle hover effect */
        }

        /* Input Fields Styling for Form */
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 14px;
            color: #333;
        }

        input[type="text"]:focus, input[type="email"]:focus {
            border-color: #4CAF50; /* Green border on focus */
            outline: none;
        }

        /* Form Heading */
        h3 {
            color: #333;
            font-size: 20px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<header>
<h1 style="color: white;">Admin Panel</h1>

    <!-- Avatar and Dropdown Info Section -->
    <div class="avatar-container">
        <img src="../static/image/avatar.jpg" alt="User Avatar" onclick="toggleDropdown()"> <!-- Click triggers dropdown -->
        <div class="dropdown-content">
            <p><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
            <button onclick="logout()">Logout</button>
        </div>
    </div>
</header>

<div class="admin-panel-container">
    <!-- Button to Add New User -->
    <button onclick="showAddUserForm()">Add New User</button>
    <button onclick="redirectToPaymentPage()">Payment Details</button>

    <!-- Add User Form -->
    <div class="form-container" id="add-user-form" style="display: none;">
        <h3>Add New User</h3>
        <form method="POST">
            <input type="text" name="user_id" placeholder="User ID" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>

    <!-- Edit User Form -->
    <?php if (isset($user)): ?>
    <div class="form-container">
        <h3>Edit User</h3>
        <form method="POST" action="index.php">
            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
            <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            <button type="submit" name="edit_user">Update User</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- User Table -->
    <table class="user-table">
        <thead>
            <tr>
                <th>S.NO</th>
                <th>User ID</th>
                <th>Email</th>
                <th>Full Name</th>
                <th>Created At</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $index => $user): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['full_name']; ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td><?php echo $user['admin'] == 1 ? 'Yes' : 'No'; ?></td>
                    <td>
                        <button class="actions-btn" onclick="editUser('<?php echo $user['user_id']; ?>')">Edit</button>
                        <button class="actions-btn" onclick="deleteUser('<?php echo $user['user_id']; ?>')">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Function to delete a user
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        window.location.replace('delete_user.php?user_id=' + userId);
    }
}

// Toggle visibility of the dropdown when clicking the avatar
function toggleDropdown() {
    const dropdown = document.querySelector('.dropdown-content');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}
    
// Hide dropdown if clicked outside
document.addEventListener('click', function (event) {
    const avatarContainer = document.querySelector('.avatar-container');
    const dropdown = document.querySelector('.dropdown-content');

    if (!avatarContainer.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});

// Logout function
function logout() {
    window.location.href = '../logout.php'; // Redirect to logout page
}

// Function to show Add User form
function showAddUserForm() {
    const form = document.getElementById('add-user-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// Function to edit user (redirects to pre-filled edit form)
function editUser(userId) {
    window.location.href = 'index.php?edit_user_id=' + userId;
}

// Redirect to the desired page when the button is clicked
function redirectToPaymentPage() {
    window.location.href = 'payment.php'; // Replace with your target page
}
</script>
</body>
</html>