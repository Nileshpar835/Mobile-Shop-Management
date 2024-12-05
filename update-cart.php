<?php
session_start();
include('includes/config.php');

if (isset($_POST['id']) && isset($_POST['quantity'])) {
    $id = intval($_POST['id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0 && isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] = $quantity;

        // Get product details from the database
        $query = mysqli_query($con, "SELECT productPrice, shippingCharge FROM products WHERE id = '$id'");
        $product = mysqli_fetch_assoc($query);

        $productPrice = $product['productPrice'];
        $shippingCharge = $product['shippingCharge'];

        // Calculate subtotal and total price
        $subtotal = $quantity * $productPrice + $shippingCharge;

        $totalprice = 0;
        foreach ($_SESSION['cart'] as $key => $cartItem) {
            $cartQuery = mysqli_query($con, "SELECT productPrice, shippingCharge FROM products WHERE id = '$key'");
            $cartProduct = mysqli_fetch_assoc($cartQuery);
            $totalprice += $cartItem['quantity'] * $cartProduct['productPrice'] + $cartProduct['shippingCharge'];
        }

        // Send updated values back to the client
        echo json_encode([
            'subtotal' => $subtotal,
            'totalprice' => $totalprice,
        ]);
    } else {
        echo json_encode(['error' => 'Invalid quantity']);
    }
}
?>
