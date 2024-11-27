<?php
session_start();
include 'db_config.php';

// Ensure order details are available
if (!isset($_POST['name']) || !isset($_POST['email'])) {
    header('Location: payment.php');
    exit();
}

// Get form submission data
$name = $_POST['name'];
$email = $_POST['email'];

// Calculate total price
$total_price = 0;
$order_items = [];

// Check if last order exists in session
$cart_to_display = isset($_SESSION['last_order']) ? $_SESSION['last_order'] : [];

if (!empty($cart_to_display)) {
    foreach ($cart_to_display as $id => $item) {
        $sql = "SELECT * FROM items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $item_total = $product['price'] * $item['quantity'];
            $total_price += $item_total;

            $order_items[] = [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'item_total' => $item_total
            ];
        }
    }

    // Generate unique order number
    $order_number = 'RW-' . strtoupper(substr(uniqid(), -6));

    // Store order in database
    $insert_order = $conn->prepare("INSERT INTO orders (order_number, customer_name, email, total_price, order_date) VALUES (?, ?, ?, ?, NOW())");
    $insert_order->bind_param("sssd", $order_number, $name, $email, $total_price);
    $insert_order->execute();

    // Clear last order
    unset($_SESSION['last_order']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ReWear - Order Receipt</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            padding: 30px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #28a745;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .order-details, .order-items {
            margin-bottom: 20px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .total-row {
            font-weight: bold;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .continue-shopping {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <?php if (!empty($order_items)): ?>
        <div class="receipt-header">
            <h1>ReWear</h1>
            <h2>Order Receipt</h2>
            <p>Order Number: <?php echo $order_number; ?></p>
        </div>

        <div class="order-details">
            <h3>Customer Information</h3>
            <p>Name: <?php echo htmlspecialchars($name); ?></p>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
        </div>

        <div class="order-items">
            <h3>Order Items</h3>
            <?php foreach ($order_items as $item): ?>
                <div class="item-row">
                    <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo htmlspecialchars($item['quantity']); ?>)</span>
                    <span>$<?php echo number_format($item['item_total'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="item-row total-row">
                <span>Total:</span>
                <span>$<?php echo number_format($total_price, 2); ?></span>
            </div>
        </div>

        <a href="display.php" class="continue-shopping">Continue Shopping</a>
        <?php else: ?>
        <div class="receipt-header">
            <h2>No Order Found</h2>
            <p>Please add items to your cart and complete the purchase.</p>
            <a href="display.php" class="continue-shopping">Return to Shop</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>