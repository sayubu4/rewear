
<?php
// checkout.php
session_start();
include 'db_config.php';

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$total = $_POST['total'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    // Generate order number
    $order_number = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // Clear cart after order
    $_SESSION['cart'] = [];
    
    // Redirect to receipt
    header("Location: receipt.php?order=$order_number&total=$total");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        .checkout-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }
        .checkout-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .payment-methods {
            margin: 20px 0;
        }
        .payment-method {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        .payment-method.selected {
            border-color: #27ae60;
            background: #f7fdf9;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Checkout</h1>
        
        <form method="POST" class="checkout-form">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" required>
            </div>
            
            <div class="payment-methods">
                <h3>Select Payment Method</h3>
                <div class="payment-method selected">
                    <input type="radio" name="payment_method" value="credit_card" checked>
                    Credit Card
                </div>
                <div class="payment-method">
                    <input type="radio" name="payment_method" value="paypal">
                    PayPal
                </div>
            </div>
            
            <h3>Order Summary</h3>
            <p>Total: $<?php echo number_format($total, 2); ?></p>
            
            <input type="hidden" name="confirm_order" value="1">
            <button type="submit" class="checkout-button">Complete Order</button>
        </form>
    </div>
</body>
</html>

<?php
// receipt.php
