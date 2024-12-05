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
    SELECT o.id AS orderId, u.name AS userName,u.shippingAddress AS shippingAdd,u.shippingState AS shippingState,u.shippingCity AS shippingCity,u.shippingPincode AS ShippingPin, p.productName AS productName, 
           o.quantity, o.orderDate 
    FROM orders o
    JOIN products p ON o.productId = p.id
    JOIN users u ON o.userId = u.id
    WHERE o.deliveryBoyId = ? AND (o.orderStatus = 'Delivered')
";
$stmt = $con->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $con->error);
}

$stmt->bind_param("i", $deliveryBoyId);
$stmt->execute();
$result = $stmt->get_result();
$PreviousDeliveries = $result->fetch_all(MYSQLI_ASSOC);

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
        <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delivery Boy| Previous Deliveries</title>
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
                /*position: fixed;*/
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

header.head {
    position: fixed;
    top: 38px;
    left: 327px;
    width: 70%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #fff;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}


        </style>
    </head>
    <body>

        <!-- Sidebar Menu -->
        <?php include("include/sidebar.php");
                 include("include/header.php");

        ?>
        <div class="content">
<!--            <header class="head">
                <h1>Delivery Boy Dashboard</h1>
                <form action="logout.php" method="POST">
                    <button type="submit">Logout</button>
                </form>
            </header>-->
            <div class="container">
                <h2>Welcome, <?php echo htmlspecialchars($deliveryBoyName); ?></h2>
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
                <div class="form-group">
                    <h3>Previous Deliveries</h3>
                    <?php if ($PreviousDeliveries): ?>
                        <div class="table-container">
                            <table class="datatable-1 table table-bordered table-striped display">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>User Name</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Order Date</th>
                                        <th>Delivery Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($PreviousDeliveries as $pdorder): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($pdorder['orderId']); ?></td>
                                            <td><?php echo htmlspecialchars($pdorder['userName']); ?></td>
                                            <td><?php echo htmlspecialchars($pdorder['productName']); ?></td>
                                            <td><?php echo htmlspecialchars($pdorder['quantity']); ?></td>
                                            <td><?php echo htmlspecialchars($pdorder['orderDate']); ?></td>
                                            <td>
                                                <?php echo nl2br(htmlspecialchars($pdorder['shippingAdd'])); ?><br>
                                                <?php echo htmlspecialchars($pdorder['shippingCity']); ?>,
                                                <?php echo htmlspecialchars($pdorder['shippingState']); ?><br>
                                                <?php echo htmlspecialchars($pdorder['ShippingPin']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php else: ?>
                        <p>No Deliveries Available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <style>
            .table-container {
                max-height: 400px; /* Set the desired height for the scrollable area */
                overflow-y: auto; /* Add vertical scroll if content exceeds the height */
                border: 1px solid #ddd; /* Optional: add a border around the table for clarity */
                margin-top: 10px; /* Spacing from other elements */
            }

            /* Optional: Customize the scrollbar appearance for better aesthetics */
            .table-container::-webkit-scrollbar {
                width: 8px;
            }

            .table-container::-webkit-scrollbar-thumb {
                background-color: #888; /* Scrollbar thumb color */
                border-radius: 4px;
            }

            .table-container::-webkit-scrollbar-thumb:hover {
                background-color: #555; /* Scrollbar thumb color on hover */
            }

            .fot{
                align-self: center;
                font-size: 15px;
                color: #999;
                margin-top: 20px;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                display: inline-block;
                vertical-align: top;
                margin: 0px;
                padding: 0;
            }

            .dataTables_wrapper .dataTables_length {
                float: left;
            }

            .dataTables_wrapper .dataTables_filter {
                float: right;
            }

            .dataTables_length label,
            .dataTables_filter label {
                display: flex;
                align-items: center;
                gap: 5px; /* Adjust spacing between text and input */
            }

            .dataTables_length select,
            .dataTables_filter input[type="search"] {
                padding: 5px;

                font-size: 17px;
            }
            .shaded {
                color: rgba(0,0,0,0.3);
                text-shadow: 0 0 1px #eee, 0 0 0 #000, 0 0 1px #fff
            }
            .datatable-pagination {
                float: right
            }
            .datatable-pagination>a {

                -webkit-border-radius: 0;
                -moz-border-radius: 0;
                border-radius: 0;
                cursor: pointer;
                display: inline-block;
                line-height: 20px;
                height: 20px;
                background-color: #f5f5f5;

                padding: 4px 12px;
                text-align: center;
                text-decoration: none!important;
                text-shadow: 0 1px 1px rgba(255,255,255,0.75);
                vertical-align: middle
            }
            .datatable-pagination>a+a {
                margin-left: -1px
            }
            .datatable-pagination>a:first-child {
                -webkit-border-top-left-radius: 3px;
                -moz-border-radius-topleft: 3px;
                border-top-left-radius: 3px;
                -webkit-border-bottom-left-radius: 3px;
                -moz-border-radius-bottomleft: 3px;
                border-bottom-left-radius: 3px
            }
            .datatable-pagination>a:last-child {
                -webkit-border-top-right-radius: 3px;
                -moz-border-radius-topright: 3px;
                border-top-right-radius: 3px;
                -webkit-border-bottom-right-radius: 3px;
                -moz-border-radius-bottomright: 3px;
                border-bottom-right-radius: 3px
            }
            .datatable-pagination>a:hover {
                background-color: #efefef;
                background-image: -moz-linear-gradient(top, #f5f5f5, #e6e6e6);
                background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f5f5f5), to(#e6e6e6));
                background-image: -webkit-linear-gradient(top, #f5f5f5, #e6e6e6);
                background-image: -o-linear-gradient(top, #f5f5f5, #e6e6e6);
                background-image: linear-gradient(to bottom, #f5f5f5, #e6e6e6);
                background-repeat: repeat-x;
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff5f5f5', endColorstr='#ffe6e6e6', GradientType=0)
            }
            .datatable-pagination>a.paginate_disabled_previous, .datatable-pagination>a.paginate_disabled_next {


                cursor: default
            }
            .datatable-pagination>a.paginate_disabled_previous i, .datatable-pagination>a.paginate_disabled_next i {
                opacity: .5;
                filter: alpha(opacity=50)
            }
            .datatable-pagination>a span {
                display: none
            }
            .btn-group>.btn:first-child {
                -webkit-border-top-left-radius: 3px;
                -moz-border-radius-topleft: 3px;
                border-top-left-radius: 3px;
                -webkit-border-bottom-left-radius: 3px;
                -moz-border-radius-bottomleft: 3px;
                border-bottom-left-radius: 3px
            }
            .btn-group>.btn:last-child {
                -webkit-border-top-right-radius: 3px;
                -moz-border-radius-topright: 3px;
                border-top-right-radius: 3px;
                -webkit-border-bottom-right-radius: 3px;
                -moz-border-radius-bottomright: 3px;
                border-bottom-right-radius: 3px
            }
            .btn-group.shaded-icon>.btn>i {
                color: rgba(0,0,0,0.3);
                text-shadow: 0 0 1px #eee, 0 0 0 #000, 0 0 1px #fff
            }
            .btn-group.shaded-icon>.btn:hover>i {
                color: rgba(0,0,0,0.4);
                text-shadow: 0 0 1px #ccc, 0 0 0 #000, 0 0 1px #fff
            }
            .btn-group>.btn, .btn-group>.dropdown-menu, .btn-group>.popover {
                font-size: 10px
            }
        </style>
        <div class="fot">
            <?php include('include/footer.php'); ?>
        </div>
        <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
        <script src="scripts/datatables/jquery.dataTables.js"></script>
        <script>
            $(document).ready(function () {
                $('.datatable-1').dataTable();
                $('.dataTables_paginate').addClass("btn-group datatable-pagination");
                $('.dataTables_paginate > a').wrapInner('<span />');
                $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
                $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
            });
        </script>
    </body>
</html>
