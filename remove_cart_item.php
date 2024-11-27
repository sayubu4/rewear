<?php
session_start();
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        $response['success'] = true;
        $response['message'] = 'Item removed from cart';
    } else {
        $response['message'] = 'Item not found in cart';
    }
}

echo json_encode($response);
?>