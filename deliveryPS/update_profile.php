<?php
session_start();
include("include/config.php"); // Include your database connection file
// Redirect to login page if the delivery boy is not logged in
if (!isset($_SESSION['deliveryBoyId'])) {
    header("Location: login.php");
    exit();
}

$deliveryBoyId = $_SESSION['deliveryBoyId'];

// Initialize success and error messages
$successMessage = "";
$errorMessage = "";

// Fetch current profile information if the form has not been submitted
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $query = "SELECT name, address, contactNO,profile_picture FROM deliveryboy WHERE id = ?";
    $stmt = $con->prepare($query);

// Check if statement was prepared successfully
    if (!$stmt) {
        die("Prepare failed: " . $con->error); // Display MySQL error message if prepare fails
    }

    $stmt->bind_param("i", $deliveryBoyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $updatedData = $result->fetch_assoc();

// Set session data with the current values
    $_SESSION['deliveryBoyName'] = $updatedData['name'];
    $_SESSION['deliveryBoyAddress'] = $updatedData['address'];
    $_SESSION['deliveryBoyContact'] = $updatedData['contactNO'];
    $_SESSION['profilePicture'] = $updatedData['profile_picture'];
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['new_name'])) {
        $newName = $_POST['new_name'];
        $query = "UPDATE deliveryboy SET name = ? WHERE id = ?";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            $errorMessage = "Error preparing statement for updating name: " . $con->error;
        } else {
            $stmt->bind_param("si", $newName, $deliveryBoyId);
            $stmt->execute();
            $successMessage = "Name updated successfully.";
            $_SESSION['deliveryBoyName'] = $newName;
        }
    }

    if (!empty($_POST['new_address'])) {
        $newAddress = $_POST['new_address'];
        $query = "UPDATE deliveryboy SET address = ? WHERE id = ?";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            $errorMessage = "Error preparing statement for updating address: " . $con->error;
        } else {
            $stmt->bind_param("si", $newAddress, $deliveryBoyId);
            $stmt->execute();
            $successMessage = "Address updated successfully.";
            $_SESSION['deliveryBoyAddress'] = $newAddress;
        }
    }

    if (!empty($_POST['new_contact'])) {
        $newContact = $_POST['new_contact'];
        $query = "UPDATE deliveryboy SET contact = ? WHERE id = ?";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            $errorMessage = "Error preparing statement for updating contact: " . $con->error;
        } else {
            $stmt->bind_param("si", $newContact, $deliveryBoyId);
            $stmt->execute();
            $successMessage = "Contact number updated successfully.";
            $_SESSION['deliveryBoyContact'] = $newContact;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delivery Boy| Update Profile</title>
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

            input[type="tel"],select, input[type="text"], button {
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

        <?php
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            // Allowed file types
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType = $_FILES['profile_picture']['type'];
            $fileSize = $_FILES['profile_picture']['size'];
            $maxSize = 5 * 1024 * 1024; // 5MB max size
            // Check if the file type is allowed
            if (!in_array($fileType, $allowedTypes)) {
                $errorMessage = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
            }
            // Check if the file size is within the limit
            elseif ($fileSize > $maxSize) {
                $errorMessage = "File size exceeds the 5MB limit.";
            } else {
                // Generate a unique name for the file to avoid overwriting
                $uploadDir = 'uploads/';
                $fileName = uniqid('profile_', true) . '.' . pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                $filePath = $uploadDir . $fileName;

                // Move the uploaded file to the designated directory
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                    // Update the profile picture in the database
                    $query = "UPDATE deliveryboy SET profile_picture = ? WHERE id = ?";
                    $stmt = $con->prepare($query);

                    if (!$stmt) {
                        $errorMessage = "Error preparing statement for updating profile picture: " . $con->error;
                    } else {
                        $stmt->bind_param("si", $filePath, $deliveryBoyId);
                        $stmt->execute();
                        $successMessage = "Profile picture updated successfully.";
                        $_SESSION['profilePicture'] = $filePath; // Update session variable
                    }
                } else {
                    $errorMessage = "Error uploading file. Please try again.";
                }
            }
        }
        ?>
    </head>
    <body>

        <!-- Sidebar Menu -->
        <!--        <div class="sidebar">
                    <img src="<?php echo $_SESSION['profilePicture']; ?>" alt="Profile Picture" class="profile-pic">
                    <h2><?php echo $_SESSION['deliveryBoyName']; ?></h2>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="change_password.php">Change Password</a>
                    <a href="previous-deliveries.php">Previous Deliveries</a>
        
                </div>-->
        <?php
        include("include/sidebar.php");
        include("include/header.php");
        ?>
        <!-- Main Content Area -->
        <div class="content">
            <!--            <header>
                            <h1>Delivery Boy Dashboard</h1>
                            <form action="logout.php" method="POST" style="display: inline;">
                                <button type="submit">Logout</button>
                            </form>
                        </header>-->

            <div class="container">
                <h2>Welcome, <?php echo $_SESSION['deliveryBoyName']; ?></h2>

                <div class="form-group">
                    <h1>Update Profile</h1>

                    <!-- Display success or error message -->
                    <?php if ($successMessage): ?>
                        <p class="message success"><?php echo $successMessage; ?></p>
                    <?php elseif ($errorMessage): ?>
                        <p class="message error"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>

                    <!-- Update form with pre-filled values -->
                    <!-- Update form with pre-filled values -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        <label>New Name:</label>
                        <input type="text" name="new_name" placeholder="Enter new name" 
                               value="<?php echo htmlspecialchars($_SESSION['deliveryBoyName']); ?>" 
                               pattern="[A-Za-z ]+" title="Only alphabets and spaces are allowed">

                        <label>New Address:</label>
                        <input type="text" name="new_address" placeholder="Enter new address" 
                               value="<?php echo htmlspecialchars($_SESSION['deliveryBoyAddress']); ?>">

                        <label>New Contact Number:</label>
                        <input type="tel" name="new_contact" placeholder="Enter new contact number" 
                               value="<?php echo htmlspecialchars($_SESSION['deliveryBoyContact']); ?>" 
                               maxlength="10" pattern="\d{10}" title="Please enter exactly 10 digits">

                        <label>New Profile Picture:</label>
                        <input type="file" name="profile_picture">

                        <button type="submit">Update Profile</button>
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
