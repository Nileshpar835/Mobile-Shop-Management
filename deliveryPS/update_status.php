<?php
session_start();
include("include/config.php");
// Include your database connection file

if (!isset($_SESSION['deliveryBoyId'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deliveryBoyId = $_SESSION['deliveryBoyId'];
    $status = $_POST['status'];

    // Update the status in the deliveryboy table
    $query = "UPDATE deliveryboy SET status = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $status, $deliveryBoyId);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>
