<?php
session_start();
include 'db_config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productBrand = $_POST['product_brand'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = [
            'name' => $productName,
            'brand' => $productBrand,
            'quantity' => 1
        ];
    } else {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    }

    $response['success'] = true;
    $response['message'] = 'Item added to cart';
}

echo json_encode($response);
?>


 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .cart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .continue-shopping, .remove-item {
            padding: 10px 20px;
            background-color: black;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .remove-item {
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <h1>Your Cart</h1>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="cart-item">
                    <div>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        <br>
                        Brand: <?php echo htmlspecialchars($item['brand']); ?>
                    </div>
                    <a href="?remove=<?php echo $item['id']; ?>" class="remove-item">Remove</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="display.php" class="continue-shopping">Continue Shopping</a>
        </div>
    </div>
</body>
</html> 