<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['deliveryBoyId'])) {
    header("Location: login.php");
    exit();
}

$deliveryBoyId = $_SESSION['deliveryBoyId'];
$message = '';

// Fetch delivery boy's name
$query = "SELECT name FROM deliveryboy WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $deliveryBoyId);
$stmt->execute();
$deliveryBoyName = $stmt->get_result()->fetch_assoc()['name'] ?? '';

// Change password
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword != $confirmPassword) {
        $message = "New password and confirm password do not match.";
    } else {
        $query = "SELECT password FROM deliveryboy WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $deliveryBoyId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row && password_verify($currentPassword, $row['password'])) {
            $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE deliveryboy SET password = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("si", $newPasswordHashed, $deliveryBoyId);

            $message = $stmt->execute() ? "Password updated successfully." : "Failed to update password. Please try again.";
        } else {
            $message = "Current password is incorrect.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delivery Boy| Change Password</title>
        <style>

            /* Reset some basic styling */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Arial', sans-serif;
            }

            /* Body Styling */
            body {
                background-color: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: flex-start;
                height: 100vh;
                padding: 0 20px;
                flex-direction: column;
            }

            /* Sidebar Menu Styling */
            .sidebar {
                width: 250px;
                height: 100%;
                background-color: #2e3a45;
                position: fixed;
                top: 0;
                left: 0;
                padding: 30px 20px;
                border-radius: 0 15px 15px 0;
                color: white;
                box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar h2 {
                text-align: center;
                font-size: 22px;
                color: #fff;
                margin-bottom: 30px;
            }

            .sidebar a {
                color: #b6c0c7;
                text-decoration: none;
                padding: 15px;
                display: block;
                border-radius: 5px;
                transition: background-color 0.3s, padding-left 0.3s;
            }

            .sidebar a:hover {
                background-color: #4CAF50;
                padding-left: 30px;
            }

            .sidebar a.active {
                background-color: #4CAF50;
                padding-left: 30px;
            }

            /* Content Area Styling */
            .content {
                margin-left: 270px;
                padding: 30px;
                width: 100%;
                max-width: 1200px;
                box-sizing: border-box;
                margin-top: 30px;
            }

            /* Header Styling */
            header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 40px;
                padding: 10px;
                background-color: #fff;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            header h1 {
                color: #333;
                font-size: 24px;
            }

            header button {
                background-color: #f44336;
                border: none;
                color: white;
                padding: 10px 20px;
                font-size: 16px;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            header button:hover {
                background-color: #d32f2f;
            }

            /* Dashboard Title */
            h2 {
                color: #333;
                text-align: center;
                font-size: 28px;
                margin-bottom: 20px;
            }

            /* Form Container Styling */
            .form-container {
                background-color: #fff;
                border-radius: 10px;
                padding: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .form-container h3 {
                font-size: 22px;
                color: #333;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            label {
                font-size: 16px;
                color: #555;
                margin-bottom: 8px;
                display: block;
            }

            input[type="password"],select, input[type="text"], button {
                width: 100%;
                padding: 12px;
                font-size: 16px;
                margin-top: 5px;
                border: 1px solid #ddd;
                border-radius: 5px;
                transition: border-color 0.3s;
            }

            select:focus, input[type="text"]:focus {
                border-color: #4CAF50;
            }

            button {
                background-color: #4CAF50;
                color: white;
                cursor: pointer;
                border: none;
                transition: background-color 0.3s;
            }

            button:hover {
                background-color: #45a049;
            }

            /* Success and Error Message Styling */
            .message {
                margin: 20px 0;
                padding: 10px;
                border-radius: 5px;
                text-align: center;
            }

            .message.success {
                background-color: #eaf4ea;
                color: #28a745;
            }

            .message.error {
                background-color: #f8d7da;
                color: #dc3545;
            }

            /* Table Styling */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 30px;
            }

            table, th, td {
                border: 1px solid #ddd;
            }

            th, td {
                padding: 12px;
                text-align: center;
            }

            th {
                background-color: #4CAF50;
                color: white;
            }

            td {
                background-color: #f9f9f9;
            }

            /* Media Query for Smaller Devices */
            @media (max-width: 768px) {
                .sidebar {
                    width: 100%;
                    position: relative;
                    border-radius: 0;
                }

                .content {
                    margin-left: 0;
                    padding: 15px;
                }

                header {
                    flex-direction: column;
                    text-align: center;
                }

                header h1 {
                    margin-bottom: 20px;
                }

                header button {
                    width: 100%;
                    margin-top: 10px;
                }

                .form-container {
                    width: 100%;
                    max-width: 100%;
                }

                table {
                    font-size: 14px;
                }
            }

            /* Animation for Sidebar Menu */
            @keyframes slideIn {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(0);
                }
            }

            .sidebar {
                animation: slideIn 0.5s ease-out;
            }

            .profile-pic {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                object-fit: cover;
                margin-bottom: 15px;
                display: block;
                margin-left: auto;
                margin-right: auto;
            }

        </style>

    </style>
</head>
<body>

   <?php include("include/sidebar.php");
                 include("include/header.php");

        ?>

    <!-- Main Content Area -->
    <div class="content">
<!--        <header>
            <h1>Delivery Boy Dashboard</h1>
            <form action="logout.php" method="POST" style="display: inline;">
                <button type="submit">Logout</button>
            </form>
        </header>-->

        <div class="container">
            <h2>Welcome, <?php echo $_SESSION['deliveryBoyName']; ?></h2>

            <div class="form-group">
                <h1>Change Password</h1>
                <form method="POST">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" name="currentPassword" id="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" name="newPassword" id="newPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" name="confirmPassword" id="confirmPassword" required>
                    </div>
                    <button type="submit">Update Password</button>
                    <?php if ($message): ?>
                        <p class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : ''; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </p>
                    <?php endif; ?>
                </form>


            </div>
        </div>
    </div>
    <style>
        .fot{
            align-self: center;
            font-size: 15px;
            color: #999;
            margin-top: 20px;
        }
    </style>
    <div class="fot">
        <?php include('include/footer.php'); ?>
    </div>
</body>
</html>
