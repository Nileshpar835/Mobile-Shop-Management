<?php
session_start();

include_once 'include/config.php';
include('../phpmailer/smtp/PHPMailerAutoload.php'); // Include PHPMailer for sending emails

if (strlen($_SESSION['alogin']) == 0) {
    header('location:login.php');
} else {
    $oid = intval($_GET['oid']);
    $email = $_GET['email'];

    if (isset($_POST['submit2'])) {
        $status = $_POST['status'];
        $remark = $_POST['remark'];

        $query = mysqli_query($con, "insert into ordertrackhistory(orderId, status, remark) values('$oid', '$status', '$remark')");
        $sql = mysqli_query($con, "update orders set orderStatus='$status' where id='$oid'");
        echo "<script>alert('Order updated successfully...');</script>";
    }
    ?>
    <?php

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
        $mail->Password = "Your pass";
        $mail->SetFrom("Your mail");
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
            <div class='header'><h1>Mobile Shop - OTP Verification for Delivery</h1></div>
            <div class='banner'>
                <img src='$banner_url' alt='Mobile Shop Banner' style='width:100%; max-width:600px; border-radius: 8px;' />
            </div>
            <div class='content'>
                <p>Dear Customer,</p>
                <p>Your OTP code for confirming delivery of your order is:</p>
                <div class='otp-code'>$otp</div>
                <p>Please provide this code to the delivery person to confirm receipt of your order. This code is valid only for a limited time.</p>
                <div class='instructions'>
                    <p><strong>Instructions for Customers:</strong></p>
                    <ul>
                        <li>Ensure you provide this code only after receiving your order.</li>
                        <li>Keep this code secure and do not share it with anyone except the delivery person.</li>
                        <li>For any issues with your order, please contact our support team promptly.</li>
                    </ul>
                    <p>Thank you for choosing Mobile Shop! We appreciate your trust in our service.</p>
                </div>
            </div>
            <div class='footer'>
                <p>If you have any questions, please contact us at noreplymobileotp.shop@gmail.com</p>
                                    <p>Nileshpar835</p>
                <p>Thank you for shopping with Mobile Shop!</p>
            </div>
        </div>
    </body>
    </html>
    ";
    }

    if (isset($_POST['send_otp'])) {
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
    ?>

    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title>Delivery Boy | Update Order</title>
            <link href="style.css" rel="stylesheet" type="text/css" />
            <link href="anuj.css" rel="stylesheet" type="text/css">
                <style>                   /* General Reset and Box Model */
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                        font-family: Arial, sans-serif;
                    }

                    /* Body */
                    body {
                        background: #f4f4f9;
                        color: #333;
                        padding: 20px;
                    }

                    /* Main Container */
                    div {
                        margin-left: 50px;
                        background: #fff;
                        border-radius: 10px;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                        padding: 20px;
                    }

                    /* Table Styling */
                    table {
                        width: 100%;
                        margin-top: 10px;
                        border-collapse: collapse;
                    }

                    table td {
                        padding: 10px;
                        text-align: left;
                    }

                    /* Heading Styles */
                    .fontpink2 {
                        font-size: 24px;
                        color: #d9534f;
                        font-weight: bold;
                        text-align: center;
                        margin-bottom: 20px;
                    }

                    /* Label Styling */
                    .fontkink1 {
                        font-size: 16px;
                        font-weight: bold;
                        color: #333;
                    }

                    /* Table Data */
                    .fontkink {
                        font-size: 16px;
                        color: #555;
                    }

                    /* Button Styling */
                    a,input[type="submit"] {
                        padding: 10px 20px;
                        background-color: #d9534f;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        transition: background-color 0.3s ease;
                    }

                    input[type="submit"]:hover {
                        background-color: #c9302c;
                    }

                    textarea {
                        width: 100%;
                        padding: 10px;
                        border: 1px solid #ccc;
                        border-radius: 5px;
                        resize: vertical;
                    }

                    select {
                        width: 100%;
                        padding: 10px;
                        border-radius: 5px;
                        border: 1px solid #ccc;
                        background-color: #f8f9fa;
                    }

                    /* Table Row Hover Effects */
                    table tr:nth-child(even) {
                        background-color: #f9f9f9;
                    }

                    table tr:hover {
                        background-color: #f1f1f1;
                        cursor: pointer;
                    }

                    /* Form Inputs */
                    input[type="text"], input[type="number"], input[type="date"], input[type="email"], input[type="password"], input[type="textarea"] {
                        padding: 10px;
                        margin: 10px 0;
                        border-radius: 5px;
                        width: 100%;
                        border: 1px solid #ddd;
                    }

                    textarea {
                        height: 100px;
                        resize: none;
                    }

                    /* Responsive Styles */
                    @media (max-width: 768px) {
                        div {
                            margin-left: 20px;
                            padding: 15px;
                        }

                        table {
                            width: 100%;
                        }

                        input[type="submit"], textarea, select {
                            width: 100%;
                        }
                    }
                    button {
                        font-family: Arial, sans-serif;
                        font-size: 16px;
                        padding: 10px 20px;
                        border-radius: 5px;
                        border: none;
                        cursor: pointer;
                        transition: background-color 0.3s ease;
                        margin: 5px;
                    }

                    #sendOtpButton {
                        background-color: #4CAF50; /* Green color for Send OTP */
                        color: white;
                    }

                    #sendOtpButton:hover {
                        background-color: #45a049;
                    }

                    button:not(#sendOtpButton) {
                        background-color: #f44336; /* Red color for Verify OTP */
                        color: white;
                    }

                    button:not(#sendOtpButton):hover {
                        background-color: #d32f2f;
                    }
                    input[type="email"][readonly] {
                        opacity: 0.6; /* Makes the input slightly transparent */
                        filter: blur; /* Adds a slight blur */
                        pointer-events: none; /* Prevents any interaction */
                        background-color: #f4f4f4; /* Light background color to show it’s inactive */
                        color: #666; /* Dimmed text color */
                        border: 1px solid #ddd; /* Light border */
                        padding: 10px;
                        font-size: 16px;
                        border-radius: 5px;
                    }


                </style>
        </head>
        <body>

            <div style="margin-left:50px;">
                <form name="updateticket" id="updateticket" method="post"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">

                        <tr height="50">
                            <td colspan="2" class="fontkink2" style="padding-left:0px;">
                                <div class="fontpink2"><b>Update Order!</b></div>
                            </td>
                        </tr>
                        <tr height="30">
                            <td class="fontkink1"><b>Order ID:</b></td>
                            <td class="fontkink"><?php echo $oid; ?></td>
                        </tr>

                        <?php
                        $ret = mysqli_query($con, "SELECT * FROM ordertrackhistory WHERE orderId='$oid'");
                        while ($row = mysqli_fetch_array($ret)) {
                            ?>
                            <tr height="20">
                                <td class="fontkink1"><b>At Date:</b></td>
                                <td class="fontkink"><?php echo $row['postingDate']; ?></td>
                            </tr>
                            <tr height="20">
                                <td class="fontkink1"><b>Status:</b></td>
                                <td class="fontkink"><?php echo $row['status']; ?></td>
                            </tr>
                            <tr height="20">
                                <td class="fontkink1"><b>Remark:</b></td>
                                <td class="fontkink"><?php echo $row['remark']; ?></td>
                            </tr>
                            <tr><td colspan="2"><hr /></td></tr>
                        <?php } ?>

                        <?php
                        $st = 'Delivered';
                        $rt = mysqli_query($con, "SELECT * FROM orders WHERE id='$oid'");
                        while ($num = mysqli_fetch_array($rt)) {
                            $currrentSt = $num['orderStatus'];
                        }
                        if ($st == $currrentSt) {
                            ?>
                            <tr><td colspan="2"><b>Product Delivered</b></td></tr>
                        <?php } else { ?>
                            <tr height="50">
                                <td class="fontkink1">Status:</td>
                                <td class="fontkink">
                                    <select name="status" class="fontkink" required="required" onchange="toggleOtpField(this)">
                                        <option value="">Select Status</option>
                                        <option value="in Process">In Process</option>
                                        <option value="Delivered">Delivered</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="otpRow" style="display: none;">
                                <td class="fontkink1">OTP:</td>
                                <td class="fontkink">
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly />
                                    <button type="button" id="sendOtpButton" onclick="sendOtp()">Send OTP</button>
                                    <button type="button" onclick="verifyOtp()">Verify OTP</button>
                                    <input type="text" name="user_otp" placeholder="Enter OTP" />

                                </td>
                            </tr>
                            <tr>
                                <td class="fontkink1">Remark:</td>
                                <td class="fontkink"><textarea cols="50" rows="7" name="remark" required="required"></textarea></td>
                            </tr>
                            <tr>
                                <td class="fontkink"></td>
                                <td class="fontkink">
                                    <input type="submit" name="submit2" value="Update" size="40" style="cursor: pointer;" />
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2">
                                <a href="http://localhost/MOB/deliveryPS/dashboard.php" style="display: inline-block; margin-top: 10px; text-decoration: none; padding: 10px 20px; background-color: #d9534f; color: white; border-radius: 5px;">Back</a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <!-- Inside the HTML form add this hidden input -->

            <!-- Update sendOtp() function with corrections and improvements -->
            <script type="text/javascript">
                var otpDelay = 30;
                function sendOtp() {
                    var email = $('#email').val();
                    if (!email) {
                        alert("Please enter your email address.");
                        return;
                    }
                    var sendOtpButton = $('#sendOtpButton');
                    sendOtpButton.prop('disabled', true);
                    sendOtpButton.text(otpDelay + " seconds remaining");

                    var countdown = otpDelay;
                    var countdownInterval = setInterval(function () {
                        countdown--;
                        sendOtpButton.text(countdown + " seconds remaining");
                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            sendOtpButton.prop('disabled', false);
                            sendOtpButton.text("Send OTP");
                        }
                    }, 1000);

                    $.ajax({
                        url: window.location.href, // Use current file URL
                        method: "POST",
                        data: {send_otp: 1, email: email},
                        success: function (response) {
                            alert(response);
                            $('#otpRow').show();
                        }
                    });
                }

                function verifyOtp() {
                    var otp = $('[name="user_otp"]').val();
                    if (!otp) {
                        alert("Please enter the OTP.");
                        return;
                    }

                    $.ajax({
                        url: window.location.href,
                        method: "POST",
                        data: {verify_otp: 1, otp: otp},
                        success: function (response) {
                            alert(response);
                            if (response.includes("successfully")) {
                                document.querySelector('input[name="submit2"]').disabled = false;
                            }
                        }
                    });
                }

                function toggleOtpField(selectElement) {
                    var otpRow = document.getElementById('otpRow');
                    otpRow.style.display = selectElement.value === 'Delivered' ? 'table-row' : 'none';
                }
            </script>
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
<?php } ?> 
