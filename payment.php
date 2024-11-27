<?php
session_start();
include 'db_config.php';
// Verify cart is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Store current cart as last order
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $_SESSION['last_order'] = $_SESSION['cart'];
    
    // Completely clear the cart after storing last order
    unset($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Information</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 400px;
        }
        .payment-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .submit-payment {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Payment Information</h2>
        <form class="payment-form" action="receipt.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="card_number" placeholder="Card Number" required>
            <input type="text" name="expiry" placeholder="MM/YY" required>
            <input type="text" name="cvv" placeholder="CVV" required>
            <button type="submit" class="submit-payment">Complete Purchase</button>
        </form>
    </div>
</body>
</html>