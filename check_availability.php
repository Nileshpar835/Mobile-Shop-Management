<?php 
session_start();
require_once("includes/config.php"); // Include your database configuration file

if (!empty($_POST["email"])) {
    $email = mysqli_real_escape_string($con, $_POST["email"]);
    $result = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");

    if ($result) {
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo "<span style='color:red;'>Email already exists.</span>";
        } else {
            echo "<span style='color:green;'>Email available for registration.</span>";
        }
    } else {
        echo "<span style='color:red;'>Error checking email availability.</span>";
    }
} else {
    echo "<span style='color:red;'>No email provided.</span>";
}
?>
