<?php
session_start();
error_reporting(0);
include("include/config.php");
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $ret = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' and password='$password'");
    $num = mysqli_fetch_array($ret);
    if ($num > 0) {
        $extra = "user-logs.php"; //
        $_SESSION['alogin'] = $_POST['username'];
        $_SESSION['id'] = $num['id'];
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("location:http://$host$uri/$extra");
        exit();
    } else {
        $_SESSION['errmsg'] = "Invalid username or password";
        $extra = "index.php";
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("location:http://$host$uri/$extra");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shopping Portal | Admin login</title>
        <!--        <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
                <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
                <link type="text/css" href="css/theme.css" rel="stylesheet">
                <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">-->
        <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
        <style>
            /* Style for "Back to Portal" link */
            .navbar .nav li a[href="http://localhost/MOB/index.php"] {
                color: #ffcc00; /* Change this color to your desired one */
                font-weight: bold;
                transition: color 0.3s ease;
            }

            .navbar .nav li a[href="http://localhost/MOB/index.php"]:hover {
                color: #ffd633; /* Hover color for "Back to Portal" */
            }

            /* Navbar styling */
            .navbar {
                background: rgba(255, 255, 255, 0.2);
                padding: 10px 20px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(8px);
                position: fixed;
                width: 100%;
                top: 0;
                z-index: 1000;
            }

            .navbar .container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .navbar .brand {
                font-size: 20px;
                color: #ffffff;
                font-weight: bold;
                text-decoration: none;
            }

            .navbar .brand:hover {
                color: #4c669f;
            }

            .navbar .nav {
                list-style: none;
                padding: 0;
                margin: 0;
                display: flex;
                align-items: center;
            }

            .navbar .nav li {
                margin-left: 20px;
            }

            .navbar .nav li a {
                color: #ffffff;
                font-size: 16px;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .navbar .nav li a:hover {
                color: #4c669f;
            }

            /* Adjusting form position under the navbar */
            .wrapper {
                margin-top: 80px; /* Space below the navbar */
            }

            /* Responsive navbar for smaller screens */
            @media (max-width: 768px) {
                .navbar .container {
                    flex-direction: column;
                }

                .navbar .nav {
                    margin-top: 10px;
                }
            }

            /* Fullscreen background with a gradient */
            body {
                font-family: 'Open Sans', sans-serif;
                background: linear-gradient(135deg, #4c669f, #3b5998, #192f6a);
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
                overflow: hidden;
            }

            /* Centering the form container */
            .wrapper .container {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100%;
            }

            /* Login form container with glassmorphism effect */
            .module-login {
                background: rgba(255, 255, 255, 0.1);
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
                backdrop-filter: blur(10px);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                border: 1px solid rgba(255, 255, 255, 0.3);
                width: 100%;
                max-width: 400px;
            }

            .module-login:hover {
                transform: scale(1.02);
                box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.4);
            }

            /* Form header */
            .module-head h3 {
                font-size: 26px;
                color: #ffffff;
                text-align: center;
                margin-bottom: 20px;
                font-weight: 600;
            }

            /* Error message styling */
            span[style="color:red;"] {
                display: block;
                font-size: 14px;
                color: #ff4d4d;
                text-align: center;
                margin-bottom: 15px;
            }

            /* Input fields styling */
            input[type="text"],
            input[type="password"] {
                width: 88%;
                padding: 12px 15px;
                margin: 10px 0;
                border-radius: 5px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                font-size: 16px;
                background: rgba(255, 255, 255, 0.2);
                color: #ffffff;
                box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3);
                transition: border-color 0.3s ease, background 0.3s ease;
            }

            input[type="text"]::placeholder,
            input[type="password"]::placeholder {
                color: #b3c2d1;
            }

            input[type="text"]:focus,
            input[type="password"]:focus {
                border-color: #4c669f;
                background: rgba(255, 255, 255, 0.3);
                outline: none;
            }

            /* Button styling */
            .btn-primary {
                background: linear-gradient(135deg, #4c669f, #3b5998);
                border: none;
                color: #ffffff;
                font-size: 16px;
                font-weight: bold;
                padding: 12px;
                width: 100%;
                border-radius: 5px;
                cursor: pointer;
                transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #3b5998, #192f6a);
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .module-login {
                    width: 90%;
                }
            }
        </style>
    </head>
    <body>

        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                        <i class="icon-reorder shaded"></i>
                    </a>

                    <a class="brand" href="index.php">
                        Shopping Portal | Admin
                    </a>

                    <div class="nav-collapse collapse navbar-inverse-collapse">

                        <ul class="nav pull-right">

                            <li><a href="http://localhost/MOB/index.php">
                                    Back to Portal
                                </a></li>




                        </ul>
                    </div><!-- /.nav-collapse -->
                </div>
            </div><!-- /navbar-inner -->
        </div><!-- /navbar -->



        <div class="wrapper">
            <div class="container">
                <div class="row">
                    <div class="module module-login span4 offset4">
                        <form class="form-vertical" method="post">
                            <div class="module-head">
                                <h3>Sign In</h3>
                            </div>
                            <span style="color:red;" ><?php echo htmlentities($_SESSION['errmsg']); ?><?php echo htmlentities($_SESSION['errmsg'] = ""); ?></span>
                            <div class="module-body">
                                <div class="control-group">
                                    <div class="controls row-fluid">
                                        <input class="span12" type="text" id="inputEmail" name="username" placeholder="Username">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="controls row-fluid">
                                        <input class="span12" type="password" id="inputPassword" name="password" placeholder="Password">
                                    </div>
                                </div>
                            </div>
                            <div class="module-foot">
                                <div class="control-group">
                                    <div class="controls clearfix">
                                        <button type="submit" class="btn btn-primary pull-right" name="submit">Login</button>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>