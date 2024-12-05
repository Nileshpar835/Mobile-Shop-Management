<?php
include('include/config.php');
session_start();
if (strlen($_SESSION['alogin']) == 1) {
    header('location:index.php');
} else {
    if (isset($_GET['orderId'])) {
        $orderId = $_GET['orderId'];
//        $orderId = $_GET['orderId'];
        $productname = $_GET['productname'];
        $username = $_GET['username']; // Retrieve the product name
// Retrieve the product name
        // Fetch delivery boys from the database
        $deliveryBoys = mysqli_query($con, "SELECT id, name FROM deliveryboy WHERE status = 'active'");

        if (isset($_POST['submit'])) { // Check if form is submitted
            $deliveryBoyId = $_POST['deliveryBoyId'];

            // Update the order to assign the delivery boy
            $query = "UPDATE orders SET deliveryBoyId = ? WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ii", $deliveryBoyId, $orderId);

            if ($stmt->execute()) {
                echo "Delivery boy assigned successfully!";
                header("Location: pending-orders.php");
                exit();
            } else {
                echo "Error assigning delivery boy.";
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Assign Delivery Boy</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                h2 {
                    text-align: center;
                    color: #333;
                    padding: 20px;
                    background-color: #f8f9fa;
                }
                form {
                    max-width: 400px;
                    margin: 30px auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                label {
                    font-size: 16px;
                    color: #555;
                    display: block;
                    margin-bottom: 8px;
                }
                select {
                    width: 100%;
                    padding: 8px;
                    margin-bottom: 20px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    font-size: 16px;
                }
                button {
                    width: 100%;
                    padding: 10px;
                    background-color: #007bff;
                    color: white;
                    font-size: 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                button:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <h2>Assign Delivery Boy to Order</h2>
            <form method="POST" action="">
                <label for="orderid">Username: <?php echo $username; ?></label><br><br>

                <label for="orderid">Order ID: <?php echo $orderId; ?></label><br><br>
                <label for="orderid">Product: <?php echo $productname; ?></label><br><br>


                <label for="deliveryBoyId">Choose Delivery Boy:</label>
                <select name="deliveryBoyId" required>
                    <?php while ($row = mysqli_fetch_array($deliveryBoys)) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php } ?>
                </select><br><br>
                <button type="submit" name="submit">Assign</button>
            </form>
        </body>
    </html>
<?php } ?>
