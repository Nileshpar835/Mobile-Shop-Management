<?php
session_start();
include("include/config.php"); // Include your database connection file
include('../phpmailer/smtp/PHPMailerAutoload.php'); // Include PHPMailer for sending emails

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contactNo = $_POST['contactNo'];
        $password = $_POST['password'];
        $address = $_POST['address'];

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists
        $checkQuery = "SELECT * FROM deliveryboy WHERE email = ?";
        $stmt = $con->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists. Please use a different email.";
        } else {
            // Insert the new delivery boy record into the database
            $query = "INSERT INTO deliveryboy (name, email, contactNo, password, address, joiningDate, status) VALUES (?, ?, ?, ?, ?, current_timestamp(), 'active')";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssiss", $name, $email, $contactNo, $hashedPassword, $address);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}

if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    if (smtp_mailer($email, "OTP Code Delivery Person - Mobile Shop", get_otp_message($otp)) == "Sent") {
        echo "OTP sent to your email address!";
    } else {
        echo "Failed to send OTP. Please try again.";
    }
    exit();
}

// Function to verify OTP
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    if ($_SESSION['otp'] == $entered_otp) {
        echo "OTP Verified successfully!";
    } else {
        echo "Invalid OTP. Please try again!";
    }
    exit();
}

// SMTP mail function
function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->Username = "noreplymobileotp.shop@gmail.com";
    $mail->Password = "gvvppdyosffcuthg";
    $mail->SetFrom("noreplymobileotp.shop@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    return $mail->Send() ? 'Sent' : $mail->ErrorInfo;
}

// Function to generate email message with banner image, OTP, and instructions
function get_otp_message($otp) {
    $banner_url = "https://utu.ac.in/index/Horizontal.jpg";
    return "
        <html>
        <head>
            <style>
                .email-container {
                    font-family: Arial, sans-serif;
                    color: #333;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
             .header {
        text-align: center;
        padding: 10px 0;
        background-color: #4CAF50;
        color: white;
        font-size: 24px;
        position: relative;
    }

    .header h1 {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4CAF50;
        border-radius: 25px; /* Makes the text container curved */
        margin: 0;
        color: white;
    }
                .banner {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .content {
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    margin-top: 20px;
                }
                .otp-code {
                    font-size: 24px;
                    font-weight: bold;
                    color: #4CAF50;
                }
                .instructions {
                    font-size: 14px;
                    color: #555;
                    margin-top: 15px;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    color: #999;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'><h1>Mobile Shop - Delivery Person OTP Verification</h1></div>
                <div class='banner'>
                    <img src='$banner_url' alt='Mobile Shop Banner' style='width:100%; max-width:600px; border-radius: 8px;' />
                </div>
                <div class='content'>
                    <p>Dear Delivery Person,</p>
                    <p>Your OTP code for verification is:</p>
                    <div class='otp-code'>$otp</div>
                    <p>Please use this code to complete your registration process. This code is valid only for a limited time.</p>
                    <div class='instructions'>
                        <p><strong>Instructions for Delivery Personnel:</strong></p>
                        <ul>
                            <li>Ensure you complete the verification process promptly.</li>
                            <li>Once verified, follow the provided guidelines for delivery procedures.</li>
                            <li>Keep your contact information up to date for customer communication.</li>
                            <li>Always carry a valid ID and your assigned order details for verification at each delivery location.</li>
                        </ul>
                        <p>Thank you for joining Mobile Shop. We look forward to working with you!</p>
                    </div>
                </div>
                <div class='footer'>
                    <p>If you have any questions, please contact us at noreplymobileotp.shop@gmail.com.</p>
                    <p>Thank you for choosing Mobile Shop!</p>
                </div>
            </div>
        </body>
        </html>
    ";
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delivery Boy Registration</title>
        <style>
            a{
                color: #4CAF50;
            }
            body {
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
            }
            .registration-container {
                width: 400px;
                margin: 50px auto;
                padding: 20px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            }
            h2 {
                text-align: center;
                color: #333;
            }
            label {
                display: block;
                margin-top: 10px;
                color: #555;
            }
            input, textarea, button {
                width: 100%;
                padding: 10px;
                margin-top: 5px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            button {
                background-color: #4CAF50;
                color: #fff;
                cursor: pointer;
            }
            button:disabled {
                background-color: #ccc;
            }
            .error-message {
                color: red;
                text-align: center;
                margin-bottom: 10px;
            }
            #otp-countdown {
                color: #999;
                font-size: 12px;
            }
        </style>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript">
            var otpDelay = 30;
            function sendOtp() {
                var email = document.getElementById('email').value;
                if (!email) {
                    alert("Please enter your email address.");
                    return;
                }
                var sendOtpButton = document.getElementById('sendOtpButton');
                sendOtpButton.disabled = true;
                var countdownContainer = document.getElementById('otp-countdown');
                countdownContainer.innerHTML = otpDelay + " seconds remaining";
                var countdown = otpDelay;
                var countdownInterval = setInterval(function () {
                    countdown--;
                    countdownContainer.innerHTML = countdown + " seconds remaining";
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        sendOtpButton.disabled = false;
                        countdownContainer.innerHTML = "";
                        otpDelay += 30;
                    }
                }, 1000);
                $.ajax({
                    url: "",
                    method: "POST",
                    data: {send_otp: 1, email: email},
                    success: function (response) {
                        alert(response);
                        $('#otp-container').show();
                    }
                });
            }
            function verifyOtp() {
                var otp = document.getElementById('otp').value;
                $.ajax({
                    url: "",
                    method: "POST",
                    data: {verify_otp: 1, otp: otp},
                    success: function (response) {
                        alert(response);
                        if (response.includes("successfully")) {
                            document.getElementById('register').disabled = false;
                        }
                    }
                });
            }
        </script>
        <script type="text/javascript">
            function validateForm() {
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const contactNo = document.getElementById('contactNo').value;
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmpassword').value;

                // Name validation (only alphabets)
                const namePattern = /^[A-Za-z\s]+$/;
                if (!namePattern.test(name)) {
                    alert("Name can only contain alphabets.");
                    return false;
                }

                // Contact number validation (10 digits)
                const contactPattern = /^\d{10}$/;
                if (!contactPattern.test(contactNo)) {
                    alert("Contact number must be exactly 10 digits.");
                    return false;
                }

                // Email validation
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailPattern.test(email)) {
                    alert("Please enter a valid email address.");
                    return false;
                }

                // Password validation
                const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                if (!passwordPattern.test(password)) {
                    alert("Password must be at least 8 characters long, and include one digit, one uppercase letter, one lowercase letter, and one special character.");
                    return false;
                }

                // Confirm password validation
                if (password !== confirmPassword) {
                    alert("Passwords do not match.");
                    return false;
                }

                return true;
            }
        </script>

    </head>
    <body>
        <div class="registration-container">
            <h2>Register as a Delivery Boy</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <!--<form method="POST" action="">-->
            <form method="POST" action="">
                <label>Name:</label>
                <!-- Only allows alphabets and spaces, using `pattern` attribute -->
                <input type="text" name="name" id="name" required pattern="[A-Za-z\s]+" title="Name can only contain alphabets and spaces"><br>

                <label>Email:</label>
                <!-- Validates for a standard email format -->
                <input type="email" name="email" id="email" required title="Please enter a valid email address"><br>

                <button type="button" id="sendOtpButton" onclick="sendOtp()">Send OTP</button>
                <span id="otp-countdown"></span>
                <div id="otp-container" style="display: none;">
                    <label>Enter OTP:</label>
                    <input type="text" id="otp" required>
                    <button type="button" onclick="verifyOtp()">Verify OTP</button>
                </div>

                <label>Contact No:</label>
                <!-- Ensures exactly 10 digits for the phone number -->
                <input type="tel" name="contactNo" id="contactNo" maxlength="10" required pattern="\d{10}" title="Contact number must be exactly 10 digits"><br>

                <label>Address:</label>
                <textarea name="address" required></textarea><br>

                <label>Password:</label>
                <!-- Password validation for 1 digit, 1 uppercase, 1 lowercase, 1 special character, minimum 8 characters -->
                <input type="password" name="password" id="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&]).{8,}" 
                       title="Password must be at least 8 characters, include 1 digit, 1 uppercase, 1 lowercase, and 1 special character"><br>

                <label>Confirm Password:</label>
                <!-- Confirmation password field that checks if it matches the 'password' field -->
                <input type="password" name="confirmpassword" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&]).{8,}" 
                       title="Please enter the same password as above"><br>

                <button type="submit" name="register" id="register" disabled>Register</button>
            </form>

            <p>Already have an account?     <a href="login.php">Login here</a></p>

        </div>
        <style>
            .footer {
                text-align: center;
                font-size: 15px;
                color: #999;
                margin-top: 20px;
            }
        </style>
        <div class="footer">
            <?php include('include/footer.php'); ?>
        </div>
    </body>
</html>
