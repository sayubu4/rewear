<?php
include 'db_config.php';
include 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReWear - Buy Now</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* CSS styles from the previous code */
        /* ... */

        /* Search Bar Styles */
        .search-bar {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-bar input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .search-bar button {
            background-color: var(--primary-color);
            color: var(--text-light);
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .search-bar button:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">ReWear</div>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search products..." />
            <button onclick="searchProducts()">Search</button>
        </div>
        <ul class="nav-links">
            <li><a href="rewear.html">Home</a></li>
            <li><a href="about_us.html">About Us</a></li>
            <li class="dropdown">
                <a href="services.html" class="dropdown-toggle">Our Services</a>
                <ul class="dropdown-menu">
                    <li><a href="sell.php">Sell</a></li>
                    <li><a href="display.php">Buy Now</a></li>
                    <li><a href="swap_page.php">Swap</a></li>
                </ul>
            </li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="supportpage.html">FAQs</a></li>
        </ul>
        <div class="auth-buttons">
            <a href="login_user_rewear.php" class="login-btn">Login</a>
            <a href="register_user_rewear.php" class="signup-btn">Sign Up</a>
        </div>
    </nav>

    <div class="container">
        <div class="product-grid">
            <?php
            $sql = "SELECT * FROM items";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='product-card'>";
                    echo "<div class='product-image-container'>";
                    echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='" . htmlspecialchars($row['name']) . "' class='product-image' />";
                    echo "</div>";
                    echo "<div class='product-info'>";
                    echo "<h2 class='product-name'>" . htmlspecialchars($row['name']) . "</h2>";
                    echo "<div class='product-price'>$" . number_format($row['price'], 2) . "</div>";
                    echo "<div class='product-details'>";
                    echo "<p><span>Brand:</span> " . htmlspecialchars($row['brand']) . "</p>";
                    echo "<p><span>Category:</span> " . htmlspecialchars($row['category']) . "</p>";
                    echo "<p><span>Size:</span> " . htmlspecialchars($row['size']) . "</p>";
                    echo "<p><span>Color:</span> " . htmlspecialchars($row['color']) . "</p>";
                    echo "<p><span>Conditions:</span> " . htmlspecialchars($row['conditions']) . "</p>";
                    echo "<p><span>Status:</span> " . htmlspecialchars($row['swap_status']) . "</p>";
                    echo "</div>";
                    echo "<a href='view_cart.php' class='buy-button' onclick='addToCart(" . $row['id'] . ", \"" . htmlspecialchars($row['name']) . "\", \"" . htmlspecialchars($row['brand']) . "\")'>
                            Add to Cart
                        </a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='no-results'>No products found</div>";
            }
            ?>
        </div>
    </div>

    <?php
    $conn->close();
    ?>

    <script>
        function addToCart(productId, productName, productBrand) {
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&product_name=${productName}&product_brand=${productBrand}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item added to cart');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function searchProducts() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(card => {
                const productName = card.querySelector('.product-name').textContent.toLowerCase();
                const productBrand = card.querySelector('.product-details p:first-child span:last-child').textContent.toLowerCase();

                if (productName.includes(searchTerm) || productBrand.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>