<?php
if (!isset($_SESSION['deliveryBoyId'])) {
    header("Location: login.php");
    exit();
}

// Fetch the delivery boy's name and profile picture
$deliveryBoyId = $_SESSION['deliveryBoyId'];
$query = "SELECT name, profile_picture FROM deliveryboy WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $deliveryBoyId);
$stmt->execute();
$nameResult = $stmt->get_result();
$deliveryBoyData = $nameResult->fetch_assoc();
$deliveryBoyName = $deliveryBoyData['name'] ?? '';
$profilePicture = $deliveryBoyData['profile_picture'] ?? 'path/to/default/profile.png';
?>

<div class="sidebar">
    <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-pic">
    <h2><?php echo htmlspecialchars($deliveryBoyName); ?></h2>
        <a href="dashboard.php">Dashboard</a>

    <a href="change_password.php">Change Password</a>
    <a href="update_profile.php">Update Profile</a>
    <a href="previous-deliveries.php">Previous Deliveries</a>
</div>
