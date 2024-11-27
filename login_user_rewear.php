<?php
session_start();
include 'db_config.php';
include 'password_util.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $enteredPassword = $_POST['password'];

    // Prepare SQL to prevent SQL injection
    $sql = "SELECT UserID, Username, Password FROM user WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user's hashed password and ID
        $row = $result->fetch_assoc();
        $hashedPassword = $row['Password'];
        $userId = $row['UserID'];
        $username = $row['Username'];

        // Verify the password
        if (verifyPassword($enteredPassword, $hashedPassword)) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            
            // Redirect to the dashboard
            header("Location: display.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }
    
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReWear - Login</title>
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
            background-color: #f4f4f4;
        }

        /* Navbar Styles (Same as swap_page.php) */
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

        /* Login Form Styles */
        .login-container {
            max-width: 400px;
            margin: 120px auto 0;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .login-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .login-form label {
            font-weight: 500;
        }

        .login-form input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }

        .login-form input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .login-form .submit-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 25px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .login-form .submit-btn:hover {
            background: var(--secondary-color);
        }

        .login-form .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">ReWear</div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
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

    <div class="login-container">
        <h1>Login</h1>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form class="login-form" action="login_user_rewear.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Login" class="submit-btn">
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="register_user_rewear.php">Register here</a></p>
        </div>
    </div>
</body>
</html>