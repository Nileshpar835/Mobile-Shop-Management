<?php
session_start();
include("include/config.php");
date_default_timezone_set('Asia/Kolkata');
$ldate = date('Y-m-d H:i:s');  // MySQL-compatible date format

// Ensure the assignment operator is used for setting $_SESSION['login'] to an empty string
$_SESSION['login'] = "";

// Update query
mysqli_query($con, "UPDATE deliverypslog SET logout = '$ldate' WHERE userEmail = '" . $_SESSION['login'] . "' ORDER BY id DESC LIMIT 1");

// Clear all session variables
session_unset();

// Set the logout message
$_SESSION['error'] = "You have successfully logged out";
?>
<script language="javascript">
document.location = "login.php";
</script>
