<?php
session_start();
include('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    date_default_timezone_set('Asia/Kolkata'); // Set timezone
    $currentTime = date('d-m-Y h:i:s A', time());

    // Handle deletion of a delivery boy
    if (isset($_GET['del'])) {
        $deliveryBoyId = $_GET['del'];
        mysqli_query($con, "DELETE FROM deliveryboy WHERE id = '" . $deliveryBoyId . "'");
        $_SESSION['delmsg'] = "Delivery boy deleted!";
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin | Manage Delivery Boys</title>
            <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
            <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
            <link type="text/css" href="css/theme.css" rel="stylesheet">
            <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
            <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
            <style>
                body {
                    font-size: 10px;
                    font-family: 'Open Sans', Arial, sans-serif;
                    background: #fff;
                    color: #777
                }
            </style>
        </head>
        <body>
            <?php include('include/header.php'); ?>

            <div class="wrapper">
                <div class="container">
                    <div class="row">
                        <?php include('include/sidebar.php'); ?>				
                        <div class="span9">
                            <div class="content">

                                <div class="module">
                                    <div class="module-head">
                                        <h3>Manage Delivery Boys</h3>
                                    </div>
                                    <div class="module-body table">
                                        <?php if (isset($_SESSION['delmsg'])) { ?>
                                            <div class="alert alert-error">
                                                <button type="button" class="close" data-dismiss="alert">×</button>
                                                <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                                <?php unset($_SESSION['delmsg']); ?>
                                            </div>
                                        <?php } ?>

                                        <br />

                                        <table id="deliveryBoyTable" cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Contact No</th>
                                                    <th>Address</th>
                                                    <th>Registration Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $query = mysqli_query($con, "SELECT * FROM deliveryboy");
                                                $cnt = 1;
                                                while ($row = mysqli_fetch_array($query)) {
                                                    ?>									
                                                    <tr>
                                                        <td><?php echo htmlentities($cnt); ?></td>
                                                        <td><?php echo htmlentities($row['name']); ?></td>
                                                        <td><?php echo htmlentities($row['email']); ?></td>
                                                        <td><?php echo htmlentities($row['contactNo']); ?></td>
                                                        <td><?php echo htmlentities($row['address']); ?></td>
                                                        <td><?php echo htmlentities($row['joiningDate']); ?></td>
                                                        <td><a href="?del=<?php echo $row['id']; ?>" onClick="return confirm('Are you sure you want to delete?')">Delete</a></td>
                                                    </tr>
                                                    <?php $cnt++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!--/.content-->
                        </div><!--/.span9-->
                    </div>
                </div><!--/.container-->
            </div><!--/.wrapper-->

    <?php include('include/footer.php'); ?>

            <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
            <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
            <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
            <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
            <script src="scripts/datatables/jquery.dataTables.js"></script>
            <script>
                                                            $(document).ready(function () {
                                                                $('#deliveryBoyTable').dataTable();
                                                                $('.dataTables_paginate').addClass("btn-group datatable-pagination");
                                                                $('.dataTables_paginate > a').wrapInner('<span />');
                                                                $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
                                                                $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
                                                            });
            </script>
        </body>
    </html>
<?php } ?>
