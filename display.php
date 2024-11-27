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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2E7D32;
            --accent-color: #81C784;
            --text-dark: #333333;
            --text-light: #ffffff;
            --transition: all 0.3s ease-in-out;
        }

        body {
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            min-width: 180px;
            padding: 1rem 0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
            list-style: none;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li {
            padding: 0.5rem 1.5rem;
        }

        .dropdown-menu a {
            color: var(--text-dark);
            text-decoration: none;
            display: block;
        }

        .dropdown-menu a:hover {
            color: var(--primary-color);
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .auth-buttons a {
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            transition: var(--transition);
            font-weight: 500;
        }

        .login-btn {
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .login-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .signup-btn {
            background: var(--primary-color);
            color: white;
        }

        .signup-btn:hover {
            background: var(--secondary-color);
        }

        /* Product Grid Styles */
        .container {
            max-width: 1200px;
            margin: 120px auto 0;
            padding: 20px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            padding: 20px 0;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .product-image-container {
            position: relative;
            padding-top: 100%;
            background: #f8f8f8;
            overflow: hidden;
        }

        .product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            line-height: 1.4;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: black;
        }

        .product-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-details p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }

        .product-details span {
            font-weight: 600;
            color: #444;
        }

        .buy-button {
            background: black;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: auto;
            text-align: center;
            text-decoration: none;
        }

        .buy-button:hover {
            background: var(--primary-color);
        }

        .no-results {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px;
            color: #666;
            font-size: 1.1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 16px;
            }
            
            .container {
                padding: 12px;
            }
            
            .product-info {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">ReWear</div>
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
</script>

</body>
</html>