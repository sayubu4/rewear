<?php
session_start();
include 'db_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReWear - Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }
        .item-details {
            flex-grow: 1;
        }
        .item-price {
            font-weight: bold;
        }
        .cart-summary {
            margin-top: 20px;
            text-align: right;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .remove-item {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;

        }
        .cart-actions {
            margin-top: 20px;
        }
        .proceed-payment {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        
    </style>
</head>
<body>
    <div class="cart-container">
        <h1>Your Cart</h1>
        <?php
        $total_price = 0;
        $total_quantity = 0;

        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $id => $item) {
                // Fetch additional product details from database
                $sql = "SELECT * FROM items WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();

                if ($product) {
                    $item_total = $product['price'] * $item['quantity'];
                    $total_price += $item_total;
                    $total_quantity += $item['quantity'];

                    echo "<div class='cart-item'>";
                    echo "<img src='" . htmlspecialchars($product['image_path']) . "' alt='" . htmlspecialchars($item['name']) . "' class='item-image'>";
                    echo "<div class='item-details'>";
                    echo "<h2>" . htmlspecialchars($item['name']) . "</h2>";
                    echo "<p>Brand: " . htmlspecialchars($item['brand']) . "</p>";
                    echo "<p class='item-price'>Price: $" . number_format($product['price'], 2) . "</p>";
                    echo "</div>";
                    echo "<div class='item-quantity'>";
                    echo "<p>Quantity: " . htmlspecialchars($item['quantity']) . "</p>";
                    echo "<p>Item Total: $" . number_format($item_total, 2) . "</p>";
                    echo "<button class='remove-item' onclick='removeItem(" . $id . ")'>Remove</button>";
                    echo "</div>";
                    echo "</div>";
                }
            }

            // Cart Summary
            echo "<div class='cart-summary'>";
            echo "<h3>Cart Summary</h3>";
            echo "<p>Total Quantity: " . $total_quantity . "</p>";
            echo "<p>Total Price: $" . number_format($total_price, 2) . "</p>";
            echo "</div>";

            echo "<div class='cart-actions'>";
            echo "<button class='proceed-payment' onclick='window.location.href=\"payment.php\"'>Proceed to Payment</button>";
            echo "</div>";
        } else {
            echo "<p>Your cart is empty</p>";
        }
        ?>
    </div>

    <script>
    function removeItem(productId) {
        fetch('remove_cart_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>
</body>
</html>