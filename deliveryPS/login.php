<?php
session_start();
include("include/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query to retrieve delivery boy by email
    $query = "SELECT * FROM deliveryboy WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $deliveryBoy = $result->fetch_assoc();

    // Get user IP address
    $userIp = $_SERVER['REMOTE_ADDR'];

    if ($deliveryBoy && password_verify($password, $deliveryBoy['password'])) {
        // Successful login, set session variables
        $_SESSION['deliveryBoyId'] = $deliveryBoy['id'];
        $_SESSION['deliveryBoyName'] = $deliveryBoy['name'];

        // Log the successful login attempt
        $status = 1;
        $logQuery = "INSERT INTO deliverypslog (userEmail, userip, status) VALUES (?, ?, ?)";
        $logStmt = $con->prepare($logQuery);
        $logStmt->bind_param("ssi", $email, $userIp, $status);
        $logStmt->execute();

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Login failed
        $error = "Invalid email or password";

        // Log the failed login attempt
        $status = 0;
        $logQuery = "INSERT INTO deliverypslog (userEmail, userip, status) VALUES (?, ?, ?)";
        $logStmt = $con->prepare($logQuery);
        $logStmt->bind_param("ssi", $email, $userIp, $status);
        $logStmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delivery Boy Login</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .login-container {
                background-color: #fff;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 400px;
                text-align: center;
            }

            h2 {
                color: #333;
                margin-bottom: 20px;
            }

            label {
                display: block;
                font-size: 16px;
                color: #555;
                margin-bottom: 8px;
                text-align: left;
            }

            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 12px;
                margin-bottom: 20px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 16px;
            }

            button {
                width: 100%;
                padding: 14px;
                background-color: #4CAF50;
                color: white;
                font-size: 16px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            button:hover {
                background-color: #45a049;
            }

            .error-message {
                color: red;
                margin-bottom: 10px;
                font-size: 14px;
            }

            .login-container p {
                margin-top: 20px;
                font-size: 14px;
            }

            .login-container a {
                text-decoration: none;
                color: #4CAF50;
            }

            .login-container a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Delivery Boy Login</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label>Email:</label>
                <input type="email" name="email" required><br>

                <label>Password:</label>
                <input type="password" name="password" required><br>

                <button type="submit">Login</button>
            </form>

            <p>Don't have an account? <a href="register.php">Register here</a></p>
            If you Forgot your Password, Please contact Administrator <a href="https://mail.google.com/" style="color: red">noreplymobileotp.shop@gmail.com.</a>
        </div>

        <div class="fot">
            <?php // include('include/footer.php'); ?>
        </div>
    </body>
</html>
