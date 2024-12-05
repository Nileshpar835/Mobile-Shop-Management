<?php
session_start();

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Remove the item from the session cart
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);

        // Recalculate the total price
        $totalprice = 0;
        if (!empty($_SESSION['cart'])) {
            include('includes/config.php');
            foreach ($_SESSION['cart'] as $key => $cartItem) {
                $query = mysqli_query($con, "SELECT productPrice, shippingCharge FROM products WHERE id = '$key'");
                $product = mysqli_fetch_assoc($query);
                $totalprice += $cartItem['quantity'] * $product['productPrice'] + $product['shippingCharge'];
            }
        }

        echo json_encode([
            'status' => 'success',
            'totalprice' => $totalprice
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Item not found in the cart']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
