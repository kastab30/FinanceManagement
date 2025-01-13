<?php
// Disable caching
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Pragma: no-cache");
// header("Expires: 0");

// Your usual session start
session_start();

// Destroy session
session_destroy();

// Redirect to login page
?>
<script>window.location.href = "index.php";</script>
<?php
exit();
?>