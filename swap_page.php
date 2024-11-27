<?php
include 'db_config.php';
include 'session_check.php';

// Determine the swap status filter
$swap_status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Prepare the query based on swap status
if ($swap_status == 'swap') {
    $query = "SELECT * FROM items WHERE swap_status = 'swap'";
} elseif ($swap_status == 'sell_swap') {
    $query = "SELECT * FROM items WHERE swap_status = 'both'";
} else {
    $query = "SELECT * FROM items WHERE swap_status IN ('swap', 'both')";
}
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReWear - Swap Items</title>
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

        /* Swap Page Specific Styles */
        .container {
            max-width: 1200px;
            margin: 120px auto 0;
            padding: 20px;
        }

        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            padding: 20px 0;
        }

        .item-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .item-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .item-card:hover img {
            transform: scale(1.05);
        }

        .item-details {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .filter-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .filter-section a {
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 25px;
            background-color: transparent;
            color: var(--text-dark);
            border: 2px solid var(--primary-color);
            transition: var(--transition);
        }

        .filter-section a.active {
            background-color: var(--primary-color);
            color: white;
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
        <div class="filter-section">
            <a href="swap_page.php?status=swap" class="<?= $swap_status == 'swap' ? 'active' : '' ?>">Swap Only</a>
            <a href="swap_page.php?status=sell_swap" class="<?= $swap_status == 'sell_swap' ? 'active' : '' ?>">Sell or Swap</a>
        </div>
        
        <div class="item-grid">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <div class="item-card" onclick="redirectToSwapForm(
                    '<?= htmlspecialchars($row['id']) ?>'
                )">
                    <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                    <div class="item-details">
                        <h2 class="product-name"><?= htmlspecialchars($row['name']) ?></h2>
                        <div class="product-details">
                            <p><span>Brand:</span> <?= htmlspecialchars($row['brand']) ?></p>
                            <p><span>Size:</span> <?= htmlspecialchars($row['size']) ?></p>
                            <p><span>Condition:</span> <?= htmlspecialchars($row['conditions']) ?></p>
                            <p><span>Swap Status:</span> <?= htmlspecialchars($row['swap_status']) ?></p>
                            <?php if ($row['swap_status'] != 'swap') { ?>
                                <p><span>Price:</span> $<?= htmlspecialchars($row['price']) ?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
                echo "<div class='no-results'>No swap items available.</div>";
            }
            ?>
        </div>
    </div>

    <script>
        function redirectToSwapForm(itemId) {
            window.location.href = 'swap_form.php?item_id=' + itemId;
        }
    </script>
</body>
</html>
<?php
mysqli_close($conn);
?>