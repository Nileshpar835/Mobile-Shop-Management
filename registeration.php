<?php
session_start();
error_reporting(0);
include('includes/config.php');
// Code user Registration
if (isset($_POST['submit'])) {
    $name = $_POST['fullname'];
    $email = $_POST['emailid'];
    $contactno = $_POST['contactno'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure hashing
    
    $email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($email_check) > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        $query = mysqli_query($con, "INSERT INTO users (name, email, contactno, password) VALUES ('$name', '$email', '$contactno', '$password')");
        if ($query) {
            echo "<script> window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Registration failed. Please try again.');</script>";
        }
    }
}

// Function to send OTP
if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    include('phpmailer/smtp/PHPMailerAutoload.php');
//    if (smtp_mailer($email, "noreplymobileshopotp", "Your OTP code is: $otp") == "Sent") {
    if (smtp_mailer($email, "Your OTP Code - Mobile Shop", get_otp_message($otp)) == "Sent") {
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
    $mail->Username = "Your mail";
    $mail->Password = "Your passcode";
    $mail->SetFrom("Your mail");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    return $mail->Send() ? 'Sent' : $mail->ErrorInfo;
}

// Function to generate email message with banner image, OTP, and instructions
function get_otp_message($otp) {
    // Replace with the URL of your banner image
    $banner_url = "https://utu.ac.in/index/Horizontal.jpg";
//    https://utu.ac.in/bmiit/images/logo_100x100.png
//    https://app.utu.ac.in/stud/images/utulogo.png
//    https://utu.ac.in/index/Horizontal.jpg

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
                .instructions {
                    margin-top: 15px;
                    font-size: 14px;
                    color: #555;
                }
                .otp-code {
                    font-size: 24px;
                    font-weight: bold;
                    color: #4CAF50;
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
                <div class='header'><h1>Welcome to Mobile Shop</h1></div>
                <div class='banner'>
                    <img src='$banner_url' alt='Mobile Shop Banner' style='width:100%; max-width:600px; border-radius: 8px;' />
                </div>
                <div class='content'>
                    <p>Dear Customer,</p>
                    <p>Your OTP code for verification is:</p>
                    <div class='otp-code'>$otp</div>
                    <p>Please use this code to complete your verification. Remember, this code is valid for a limited time.</p>
                    <div class='instructions'>
                        <p><strong>Important Instructions:</strong></p>
                        <ul>
                            <li>Do not share this OTP code with anyone, including Mobile Shop employees.</li>
                            <li>Avoid sharing your password or other sensitive information with anyone.</li>
                        </ul>
                    </div>
                </div>
                <div class='footer'>
                    <p>Thank you for choosing Mobile Shop!</p>
                    <p>Nileshpar835</p>
                    <p>Contact us at noreplymobileotp.shop@gmail.com for any assistance.</p>
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
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keywords" content="MediaCenter, Template, eCommerce">
        <meta name="robots" content="all">

        <title>Shopping Portal | Signi-in | Signup</title>

        <!-- Bootstrap Core CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">

        <!-- Customizable CSS -->
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/green.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.css">
        <link rel="stylesheet" href="assets/css/owl.transitions.css">
        <!--<link rel="stylesheet" href="assets/css/owl.theme.css">-->
        <link href="assets/css/lightbox.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/animate.min.css">
        <link rel="stylesheet" href="assets/css/rateit.css">
        <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">

        <!-- Demo Purpose Only. Should be removed in production -->
        <link rel="stylesheet" href="assets/css/config.css">

        <link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
        <link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
        <link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
        <link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
        <link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
        <!-- Demo Purpose Only. Should be removed in production : END -->


        <!-- Icons/Glyphs -->
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">

        <!-- Fonts --> 
        <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

        <!-- Favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <script type="text/javascript">
            var otpDelay = 30; // Initial delay in seconds

            function sendOtp() {
                var email = document.getElementById('email').value;

                // Check if email field is not empty
                if (!email) {
                    alert("Please enter your email address.");
                    return;
                }

                // Disable the "Send OTP" button and start the countdown
                var sendOtpButton = document.getElementById('sendOtpButton');
                sendOtpButton.disabled = true;

                // Show countdown next to the button
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
                        otpDelay += 30; // Increase delay by 30 seconds for the next click
                    }
                }, 1000);

                // AJAX request to send OTP
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
        </script>

        <script type="text/javascript">
            function valid() {
                if (document.register.password.value !== document.register.confirmpassword.value) {
                    alert("Password and Confirm Password Field do not match!");
                    document.register.confirmpassword.focus();
                    return false;
                }
                return true;
            }
        <script type="text/javascript">
            var otpDelay = 30; // Initial delay in seconds

            function sendOtp() {
                var email = document.getElementById('email').value;

                // Check if email field is not empty
                if (!email) {
                    alert("Please enter your email address.");
                    return;
                }

                // Disable the "Send OTP" button and start the countdown
                var sendOtpButton = document.getElementById('sendOtpButton');
                sendOtpButton.disabled = true;

                // Show countdown next to the button
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
                        otpDelay += 30; // Increase delay by 30 seconds for the next click
                    }
                }, 1000);

                // AJAX request to send OTP
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
        </script>

        <script type="text/javascript">
            function valid() {
                if (document.register.password.value !== document.register.confirmpassword.value) {
                    alert("Password and Confirm Password Field do not match!");
                    document.register.confirmpassword.focus();
                    return false;
                }
                return true;
            }
        <script type="text/javascript">
            var otpDelay = 30; // Initial delay in seconds

            function sendOtp() {
                var email = document.getElementById('email').value;

                // Check if email field is not empty
                if (!email) {
                    alert("Please enter your email address.");
                    return;
                }

                // Disable the "Send OTP" button and start the countdown
                var sendOtpButton = document.getElementById('sendOtpButton');
                sendOtpButton.disabled = true;

                // Show countdown next to the button
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
                        otpDelay += 30; // Increase delay by 30 seconds for the next click
                    }
                }, 1000);

                // AJAX request to send OTP
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
        </script>

        <script type="text/javascript">
            function valid() {
                if (document.register.password.value !== document.register.confirmpassword.value) {
                    alert("Password and Confirm Password Field do not match!");
                    document.register.confirmpassword.focus();
                    return false;
                }
                return true;
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
                            document.getElementById('submit').disabled = false;
                        }
                    }
                });
            }
        </script>

    </head>
    <body class="cnt-home">



        <!-- ============================================== HEADER ============================================== -->
        <header class="header-style-1">

            <!-- ============================================== TOP MENU ============================================== -->
            <?php include('includes/top-header.php'); ?>
            <!-- ============================================== TOP MENU : END ============================================== -->
            <?php include('includes/main-header.php'); ?>
            <!-- ============================================== NAVBAR ============================================== -->
            <?php include('includes/menu-bar.php'); ?>
            <!-- ============================================== NAVBAR : END ============================================== -->

        </header>

        <!-- ============================================== HEADER : END ============================================== -->
        <div class="breadcrumb">
            <div class="container">
                <div class="breadcrumb-inner">
                    <ul class="list-inline list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li class='active'>Registration</li>
                    </ul>
                </div><!-- /.breadcrumb-inner -->
            </div><!-- /.container -->
        </div><!-- /.breadcrumb -->

        <div class="body-content outer-top-bd">
            <div class="container">
                <div class="sign-in-page inner-bottom-sm">
                    <div class="row">


                        <!-- create a new account -->
                        <div class="col-md-6 col-sm-6 create-new-account">
                            <h4 class="checkout-subtitle">create a new account</h4>
                            <p class="text title-tag-line">Create your own Shopping account.</p>
                            <form class="register-form outer-top-xs" role="form" method="post" name="register" onSubmit="return valid();">
                                <div class="form-group">
                                    <label class="info-title" for="fullname">Full Name <span>*</span></label>
                                    <input type="text" class="form-control unicase-form-control text-input" id="fullname" name="fullname" required="required">
                                </div>

                                <!-- Email Input Field and OTP Send Button -->
                                <!-- Email Input Field and OTP Send Button -->
                                <div class="form-group">
                                    <label class="info-title" for="email">Email Address <span>*</span></label>
                                    <input type="email" class="form-control unicase-form-control text-input" id="email" name="emailid" required>
                                    <button type="button" class="btn btn-primary" id="sendOtpButton" onclick="sendOtp()">Send OTP</button>
                                    <span id="otp-countdown" style="margin-left: 10px; color: red;"></span> <!-- Countdown display -->
                                </div>

                                <!-- OTP Input Field (Initially Hidden) -->
                                <div id="otp-container" style="display: none;">
                                    <div class="form-group">
                                        <label class="info-title" for="otp">Enter OTP <span>*</span></label>
                                        <input type="text" class="form-control unicase-form-control text-input" id="otp" required>
                                        <button type="button" class="btn btn-primary" onclick="verifyOtp()">Verify OTP</button>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="info-title" for="contactno">Contact No. <span>*</span></label>
                                    <input type="text" class="form-control unicase-form-control text-input" id="contactno" name="contactno" maxlength="10" required >
                                </div>

                                <div class="form-group">
                                    <label class="info-title" for="password">Password. <span>*</span></label>
                                    <input type="password" class="form-control unicase-form-control text-input" id="password" name="password"  required >
                                </div>

                                <div class="form-group">
                                    <label class="info-title" for="confirmpassword">Confirm Password. <span>*</span></label>
                                    <input type="password" class="form-control unicase-form-control text-input" id="confirmpassword" name="confirmpassword" required >
                                </div>
                                <!--<button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button" id="submit">Sign Up</button><br><br>-->
                                <button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button" id="submit" >Sign Up</button>
                                <a href="login.php">Already a Customer ?</a>
                            </form>
                            <span class="checkout-subtitle outer-top-xs">Sign Up Today And You'll Be Able To :  </span>
                            <div class="checkbox">
                                <label class="checkbox">
                                    Speed your way through the checkout.
                                </label>
                                <label class="checkbox">
                                    Track your orders easily.
                                </label>
                                <label class="checkbox">
                                    Keep a record of all your purchases.
                                </label>
                            </div>
                        </div>	
                        <!-- create a new account -->			</div><!-- /.row -->
                </div>
                <?php include('includes/brands-slider.php'); ?>
            </div>
        </div>
        <?php include('includes/footer.php'); ?>
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/echo.min.js"></script>
        <script src="assets/js/jquery.easing-1.3.min.js"></script>
        <script src="assets/js/bootstrap-slider.min.js"></script>
        <script src="assets/js/jquery.rateit.min.js"></script>
        <script type="text/javascript" src="assets/js/lightbox.min.js"></script>
        <script src="assets/js/bootstrap-select.min.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/scripts.js"></script>
        <script src="switchstylesheet/switchstylesheet.js"></script>

        <script>
                                            $(document).ready(function () {
                                                $(".changecolor").switchstylesheet({seperator: "color"});
                                                $('.show-theme-options').click(function () {
                                                    $(this).parent().toggleClass('open');
                                                    return false;
                                                });
                                                S
                                            });

                                            $(window).bind("load", function () {
                                                $('.show-theme-options').delay(2000).trigger('click');
                                            });
        </script>
        <script>
//            function userAvailability() {
//                $("#loaderIcon").show();
//                $.ajax({
//                    url: "check_availability.php",
//                    data: {email: $("#email").val()},
//                    type: "POST",
//                    success: function (data) {
//                        $("#user-availability-status1").html(data);
//                        $("#loaderIcon").hide();
//
//                        // Check response content to enable or disable the submit button
//                        if (data.includes("Email already exists")) {
//                            $('#submit').prop('disabled', true);
//                        } else if (data.includes("Email available for registration")) {
//                            $('#submit').prop('disabled', false);
//                        }
//                    },
//                    error: function () {
//                        alert("Failed to check availability.");
//                        $("#loaderIcon").hide();
//                    }
//                });
//            }
        </script>
        <!-- For demo purposes – can be removed on production : End -->Z
    </body>
</html>
