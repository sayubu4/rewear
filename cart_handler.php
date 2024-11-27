<?php
// cart_handler.php
session_start();
include 'db_config.php';

// Initialize cart if doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        
        // Add to cart session
        if (!isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'price' => $product_price,
                'quantity' => 1
            ];
        } else {
            $_SESSION['cart'][$product_id]['quantity']++;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Added to cart',
            'cart_count' => count($_SESSION['cart'])
        ]);
        exit;
    }
    
    if ($action === 'remove') {
        $product_id = $_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
        header('Location: cart.php');
        exit;
    }
}
?>

