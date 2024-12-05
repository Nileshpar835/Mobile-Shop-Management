<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['deliveryBoyId'])) {
    header("Location: login.php");
    exit();
}

$deliveryBoyId = $_SESSION['deliveryBoyId'];

// Fetch the delivery boy's name and profile picture
$query = "SELECT name, profile_picture FROM deliveryboy WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $deliveryBoyId);
$stmt->execute();
$nameResult = $stmt->get_result();
$deliveryBoyData = $nameResult->fetch_assoc();
$deliveryBoyName = $deliveryBoyData['name'] ?? '';
$profilePicture = $deliveryBoyData['profile_picture'] ?? 'path/to/default/profile.png'; // Default image if none is set
// Fetch pending orders with product and user information
$query = "
    SELECT o.id AS orderId, u.name AS userName,u.email AS email,u.shippingAddress AS shippingAdd,u.shippingState AS shippingState,u.shippingCity AS shippingCity,u.shippingPincode AS ShippingPin, p.productName AS productName, 
           o.quantity, o.orderDate 
    FROM orders o
    JOIN products p ON o.productId = p.id
    JOIN users u ON o.userId = u.id
    WHERE o.deliveryBoyId = ? AND (o.orderStatus = 'pending' OR o.orderStatus = 'in Process' OR o.orderStatus IS NULL)
";
$stmt = $con->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $con->error);
}

$stmt->bind_param("i", $deliveryBoyId);
$stmt->execute();
$result = $stmt->get_result();
$pendingOrders = $result->fetch_all(MYSQLI_ASSOC);

// Fetch current status of the delivery boy
$statusQuery = "SELECT status FROM deliveryboy WHERE id = ?";
$stmt = $con->prepare($statusQuery);
$stmt->bind_param("i", $deliveryBoyId);
$stmt->execute();
$statusResult = $stmt->get_result();
$currentStatus = $statusResult->fetch_assoc()['status'] ?? 'unknown';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delivery Boy Dashboard</title>
        <style>
            /* Include your existing CSS styles here */

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

            select, input[type="text"], button {
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
            /* Profile Picture Styling */
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
    </head>
    <body>

     
        <!-- Sidebar Menu -->
        <?php include("include/sidebar.php");
                 include("include/header.php");

        ?>


        <!-- Main Content Area -->
        <div class="content">
<!--            <header>
                <h1>Delivery Boy Dashboard</h1>
                <form action="logout.php" method="POST">
                    <button type="submit">Logout</button>
                </form>
            </header>-->

            <div class="container">
                <h2>Welcome, <?php echo htmlspecialchars($deliveryBoyName); ?></h2>

                <!-- Status Update Form -->
                <div class="form-group">
                    <h3>Update Working Status</h3>
                    <form action="update_status.php" method="POST">
                        <label>Status:</label>
                        <select name="status">
                            <option value="active" <?php if ($currentStatus === 'active') echo 'selected'; ?>>Active</option>
                            <option value="inactive" <?php if ($currentStatus === 'inactive') echo 'selected'; ?>>Inactive</option>
                            <option value="leave" <?php if ($currentStatus === 'leave') echo 'selected'; ?>>On Leave</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </div>

                <!-- Orders Table -->
                <div class="form-group">
                    <h3>Assigned Orders</h3>
                    <?php if ($pendingOrders): ?>
                        <table>
                            <tr>
                                <th>Order ID</th>
                                <th>User Name</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Order Date</th>
                                <th>Delivery Address </th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($pendingOrders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['orderId']); ?></td>
                                    <td><?php echo htmlspecialchars($order['userName']); ?></td>
                                    <td><?php echo htmlspecialchars($order['productName']); ?></td>
                                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($order['orderDate']); ?></td>
                                    <!--<td><?php // echo htmlspecialchars($order['shippingAdd']);     ?>,<?php // echo htmlspecialchars($order['shippingState']);     ?>,<?php // echo htmlspecialchars($order['shippingCity']);     ?>,<?php // echo htmlspecialchars($order['ShippingPin']);     ?></td>-->
                                    <td>
                                        <?php echo nl2br(htmlspecialchars($order['shippingAdd'])); ?><br>
                                        <?php echo htmlspecialchars($order['shippingCity']); ?>, 
                                        <?php echo htmlspecialchars($order['shippingState']); ?><br>
                                        <?php echo htmlspecialchars($order['ShippingPin']); ?>
                                    </td>

                                    <td>
                                        <a href="updateorder.php?oid=<?php echo htmlspecialchars($order['orderId']); ?>&email=<?php echo htmlspecialchars($order['email']); ?>">
                                            Update Order
                                        </a>
                                    </td>


                                                                    <!--<td><a href="updateorder.php?oid=<?php // echo htmlspecialchars($order['orderId']);     ?>">Update Order</a></td>-->
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <p>No orders Assigned to you.</p>
                    <?php endif; ?>
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