<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (isset($_POST['submit'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_POST['quantity'] as $key => $val) {
            if ($val == 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $val;
            }
        }
//        echo "<script>alert('Your Cart hasbeen Updated');</script>";
    }
}
if (isset($_POST['remove_code'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_POST['remove_code'] as $key) {
            unset($_SESSION['cart'][$key]);
        }
//        echo "<script>alert('Your Cart has been Updated');</script>";
    }
}
if (isset($_POST['ordersubmit'])) {
    if (strlen($_SESSION['login']) == 0) {
        header('location:login.php');
    } else {
        $quantity = $_POST['quantity'];
        $pdd = $_SESSION['pid'];
        $value = array_combine($pdd, $quantity);
        foreach ($value as $qty => $val34) {
            mysqli_query($con, "insert into orders(userId,productId,quantity) values('" . $_SESSION['id'] . "','$qty','$val34')");
            header('location:payment-method.php');
        }
    }
}
if (isset($_POST['update'])) {
    $baddress = $_POST['billingaddress'];
    $bstate = $_POST['bilingstate'];
    $bcity = $_POST['billingcity'];
    $bpincode = $_POST['billingpincode'];
    $query = mysqli_query($con, "update users set billingAddress='$baddress',billingState='$bstate',billingCity='$bcity',billingPincode='$bpincode' where id='" . $_SESSION['id'] . "'");
    if ($query) {
//        echo "<script>alert('Billing Address has been updated');</script>";
    }
}
if (isset($_POST['shipupdate'])) {
    $saddress = $_POST['shippingaddress'];
    $sstate = $_POST['shippingstate'];
    $scity = $_POST['shippingcity'];
    $spincode = $_POST['shippingpincode'];
    $query = mysqli_query($con, "update users set shippingAddress='$saddress',shippingState='$sstate',shippingCity='$scity',shippingPincode='$spincode' where id='" . $_SESSION['id'] . "'");
    if ($query) {
//        echo "<script>alert('Shipping Address has been updated');</script>";
    }
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
        <title>My Cart</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/green.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.css">
        <link rel="stylesheet" href="assets/css/owl.transitions.css">
        <link href="assets/css/lightbox.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/animate.min.css">
        <link rel="stylesheet" href="assets/css/rateit.css">
        <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
        <link rel="stylesheet" href="assets/css/config.css">
        <link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
        <link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
        <link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
        <link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
        <link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
        <!-- Favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).on('change', '.quantity-input', function () {
                const productId = $(this).data('id');
                const quantity = $(this).val();

                if (quantity > 0) {
                    $.ajax({
                        url: 'update-cart.php',
                        type: 'POST',
                        data: {
                            id: productId,
                            quantity: quantity
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.error) {
                                alert(response.error);
                            } else {
                                // Update the subtotal for this product
                                $(`[data-id="${productId}"]`)
                                        .closest('tr')
                                        .find('.cart-grand-total-price')
                                        .text(`Rs ${response.subtotal}.00`);

                                // Update the total price
                                $('.cart-grand-total span').text(`Rs ${response.totalprice}.00`);
                                $('.total-price-basket .value').text(`${response.totalprice}.00`);
                                $('.pull-right span').text(`${response.totalprice}.00`);
                            }
                        },
                        error: function () {
                            alert('Failed to update the cart. Please try again.');
                        }
                    });
                } else {
                    alert('Quantity must be greater than 0');
                }
            });
        </script>
        <script>
            $(document).on('click', '.remove-item', function () {
                const productId = $(this).data('id');
                const $row = $(this).closest('tr'); // The row of the product

                $.ajax({
                    url: 'remove_item.php',
                    type: 'POST',
                    data: {id: productId},
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            // Remove the row from the table
                            $row.remove();

                            // Update the total price
                            $('.cart-grand-total span').text(`Rs ${response.totalprice}.00`);

                            // If the cart is empty, display a message
                            if ($('table tbody tr').length === 0) {
                                $('table').after('<p>Your cart is empty.</p>');
                            }
                        } else {
                            alert(response.message || 'Failed to remove the item.');
                        }
                    },
                    error: function () {
                        alert('Failed to remove the item. Please try again.');
                    }
                });
            });
        </script>



    </head>
    <body class="cnt-home">
        <header class="header-style-1">
            <?php include('includes/top-header.php'); ?>
            <?php include('includes/main-header.php'); ?>
            <?php include('includes/menu-bar.php'); ?>
        </header>
        <div class="breadcrumb">
            <div class="container">
                <div class="breadcrumb-inner">
                    <ul class="list-inline list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li class='active'>Shopping Cart</li>
                    </ul>
                </div><!-- /.breadcrumb-inner -->
            </div><!-- /.container -->
        </div><!-- /.breadcrumb -->
        <div class="body-content outer-top-xs">
            <div class="container">
                <div class="row inner-bottom-sm">
                    <div class="shopping-cart">
                        <div class="col-md-12 col-sm-12 shopping-cart-table ">
                            <div class="table-responsive">
                                <form name="cart" method="post">	
                                    <?php
                                    if (!empty($_SESSION['cart'])) {
                                        ?>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="cart-description item">Image</th>
                                                    <th class="cart-product-name item">Product Name</th>
                                                    <th class="cart-qty item">Quantity</th>
                                                    <th class="cart-sub-total item">Price Per unit</th>
                                                    <th class="cart-sub-total item">Shipping Charge</th>
                                                    <th class="cart-total last-item">Grand total</th>
                                                    <th class="cart-romove item">Remove</th>

                                                </tr>
                                            </thead><!-- /thead -->
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7">
                                                        <div class="shopping-cart-btn">
                                                            <span class="">
                                                                <a href="index.php" class="btn btn-upper btn-primary outer-left-xs">Continue Shopping</a>
                                                                <!--<input type="submit" name="submit" value="Update shopping cart" class="btn btn-upper btn-primary pull-right outer-right-xs">-->
                                                            </span>
                                                        </div><!-- /.shopping-cart-btn -->
                                                    </td>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                $pdtid = array();
                                                $sql = "SELECT * FROM products WHERE id IN(";
                                                foreach ($_SESSION['cart'] as $id => $value) {
                                                    $sql .= $id . ",";
                                                }
                                                $sql = substr($sql, 0, -1) . ") ORDER BY id ASC";
                                                $query = mysqli_query($con, $sql);
                                                $totalprice = 0;
                                                $totalqunty = 0;
                                                if (!empty($query)) {
                                                    while ($row = mysqli_fetch_array($query)) {
                                                        $quantity = $_SESSION['cart'][$row['id']]['quantity'];
                                                        $subtotal = $_SESSION['cart'][$row['id']]['quantity'] * $row['productPrice'] + $row['shippingCharge'];
                                                        $totalprice += $subtotal;
                                                        $_SESSION['qnty'] = $totalqunty += $quantity;
                                                        array_push($pdtid, $row['id']);
                                                        ?>
                                                        <tr>
                                                            <!--<td class="romove-item"><input type="checkbox" name="remove_code[]" value="<?php echo htmlentities($row['id']); ?>" /></td>-->

                                                            <td class="cart-image">
                                                                <a class="entry-thumbnail" href="detail.html">
                                                                    <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo $row['productImage1']; ?>" alt="" width="114" height="146">
                                                                </a>
                                                            </td>
                                                            <td class="cart-product-name-info">
                                                                <h4 class='cart-product-description'><a href="product-details.php?pid=<?php echo htmlentities($pd = $row['id']); ?>" ><?php
                                                                        echo $row['productName'];

                                                                        $_SESSION['sid'] = $pd;
                                                                        ?></a></h4>
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="rating rateit-small"></div>
                                                                    </div>
                                                                    <div class="col-sm-8">
                                                                        <?php
                                                                        $rt = mysqli_query($con, "select * from productreviews where productId='$pd'");
                                                                        $num = mysqli_num_rows($rt); {
                                                                            ?>
                                                                            <div class="reviews">
                                                                                ( <?php echo htmlentities($num); ?> Reviews )
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div><!-- /.row -->
                                                            </td>
                                                            <td class="cart-product-quantity">
                                                                <div class="quant-input">
                                                                    <input type="number" value="<?php echo $_SESSION['cart'][$row['id']]['quantity']; ?>" 
                                                                           name="quantity[<?php echo $row['id']; ?>]" 
                                                                           min="1"
                                                                           class="quantity-input" 
                                                                           data-id="<?php echo $row['id']; ?>">
                                                                </div>
                                                            </td>

                                                            <td class="cart-product-sub-total"><span class="cart-sub-total-price"><?php echo "Rs" . " " . $row['productPrice']; ?>.00</span></td>
                                                            <td class="cart-product-sub-total"><span class="cart-sub-total-price"><?php echo "Rs" . " " . $row['shippingCharge']; ?>.00</span></td>
                                                            <td class="cart-product-grand-total"><span class="cart-grand-total-price"><?php echo ($_SESSION['cart'][$row['id']]['quantity'] * $row['productPrice'] + $row['shippingCharge']); ?>.00</span></td>
                                                            <td class="cart-product-remove">
                                                                <button class="btn btn-danger remove-item" data-id="<?php echo $row['id']; ?>">Remove</button>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                $_SESSION['pid'] = $pdtid;
                                                ?>
                                            </tbody><!-- /tbody -->
                                        </table><!-- /table -->
                                </div>
                            </div><!-- /.shopping-cart-table -->	
                            <div class="col-md-4 col-sm-12 estimate-ship-tax">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span class="estimate-title">Shipping Address</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <?php
                                                    $query = mysqli_query($con, "select * from users where id='" . $_SESSION['id'] . "'");
                                                    while ($row = mysqli_fetch_array($query)) {
                                                        ?>
                                                        <div class="form-group">
                                                            <label class="info-title" for="Billing Address">Billing Address<span>*</span></label>
                                                            <textarea class="form-control unicase-form-control text-input"  name="billingaddress" required="required"><?php echo $row['billingAddress']; ?></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="info-title" for="Billing State ">Billing State  <span>*</span></label>
                                                            <input type="text" class="form-control unicase-form-control text-input" id="bilingstate" name="bilingstate" value="<?php echo $row['billingState']; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="info-title" for="Billing City">Billing City <span>*</span></label>
                                                            <input type="text" class="form-control unicase-form-control text-input" id="billingcity" name="billingcity" required="required" value="<?php echo $row['billingCity']; ?>" >
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="info-title" for="Billing Pincode">Billing Pincode <span>*</span></label>
                                                            <input type="text" class="form-control unicase-form-control text-input" id="billingpincode" name="billingpincode" required="required" value="<?php echo $row['billingPincode']; ?>" >
                                                        </div>
                                                        <button type="submit" name="update" class="btn-upper btn btn-primary checkout-page-button">Update</button>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody><!-- /tbody -->
                                </table><!-- /table -->
                            </div>

                            <div class="col-md-4 col-sm-12 estimate-ship-tax">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span class="estimate-title">Billing Address</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <?php
                                                    $query = mysqli_query($con, "select * from users where id='" . $_SESSION['id'] . "'");
                                                    while ($row = mysqli_fetch_array($query)) {
                                                        ?>

                                                        <div class="form-group">
                                                            <label class="info-title" for="Shipping Address">Shipping Address<span>*</span></label>
                                                            <textarea class="form-control unicase-form-control text-input"  name="shippingaddress" required="required"><?php echo $row['shippingAddress']; ?></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="info-title" for="Billing State ">Shipping State  <span>*</span></label>
                                                            <input type="text" class="form-control unicase-form-control text-input" id="shippingstate" name="shippingstate" value="<?php echo $row['shippingState']; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="info-title" for="Billing City">Shipping City <span>*</span></label>
                                                            <input type="text" class="form-control unicase-form-control text-input" id="shippingcity" name="shippingcity" required="required" value="<?php echo $row['shippingCity']; ?>" >
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="info-title" for="Billing Pincode">Shipping Pincode <span>*</span></label>
                                                            <input type="text" class="form-control unicase-form-control text-input" id="shippingpincode" name="shippingpincode" required="required" value="<?php echo $row['shippingPincode']; ?>" >
                                                        </div>
                                                        <button type="submit" name="shipupdate" class="btn-upper btn btn-primary checkout-page-button">Update</button>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody><!-- /tbody -->
                                </table><!-- /table -->
                            </div>
                            <div class="col-md-4 col-sm-12 cart-shopping-total">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="cart-grand-total">
                                                    Grand Total<span class="inner-left-md"><?php echo $_SESSION['tp'] = "$totalprice" . ".00"; ?></span>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead><!-- /thead -->
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="cart-checkout-btn pull-right">
                                                    <button type="submit" name="ordersubmit" class="btn btn-primary">PROCCED TO CHEKOUT</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody><!-- /tbody -->
                                </table>
                                <?php
                            } else {
                                echo "Your shopping Cart is empty";
                            }
                            ?>
                        </div>			</div>
                </div> 
                </form>
                <?php echo include('includes/brands-slider.php'); ?>
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
            });
            $(window).bind("load", function () {
                $('.show-theme-options').delay(2000).trigger('click');
            });
        </script>
    </body>
</html>