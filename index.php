<?php
error_reporting(1);
session_start();

// Check if the user is already logged in, if so, redirect to the appropriate page
if (isset($_SESSION['user_id'])) {
    // Check if the user is an admin
    if ($_SESSION['admin'] == 1) {
        ?>
   <script>window.location.href = "admin";</script>
   <?php
    } else {
        ?>
   <script>window.location.href = "dashboard";</script>
   <?php
    }
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture user input
    $user_id = $_POST['user_id'];

    // Database credentials
    include('config.php');

    // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify the admin status (if user is an admin, $_SESSION['admin'] is set to 1)
        if ($user['admin'] == 1) {
            // Admin login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['admin'] = 1; // Set admin session variable
            ?>
            <script>window.location.href = "admin/index.php";</script>
            <?php
             // Redirect to the admin panel
            exit();
        } else {
            // Regular user login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['admin'] = 0; // Set regular user session variable
            ?>
            <script>window.location.href = "userdashboard.php";</script>
            <?php // Redirect to user dashboard
            exit();
        }
    } else {
        // User ID does not exist, display an error message
        $error_message = "User ID does not exist!";
    }

    // Close prepared statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="static/css/styles.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <main class="container">
        <div class="content">
            <img src="static/image/logo.png" alt="" class="website_logo">
            <h1 class="main_title">Sign In</h1>

            <div class="or">
                <hr>
                <p>ENTER</p>
                <hr>
            </div>

            <form id="login-form" method="POST">
                <input type="text" name="user_id" maxlength="40" placeholder="USER ID" class="other_user" required>
                <p class="next_btn">Next</p>

                <!-- Display error message if user ID does not exist -->
                <?php if (isset($error_message)) : ?>
                    <p class="error" style="color: red;"><?php echo $error_message; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </main>

    <script>
        // Handle the "Next" button click without form submission if necessary
        $(".next_btn").click(function() {
            $("#login-form").submit(); // Trigger form submission
        });
    </script>
</body>
</html>
