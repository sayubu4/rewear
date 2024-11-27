<?php
// Include password utility functions
include 'password_util.php';
include 'db_config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // In a real app, hash this password
    $location = $_POST['location'];
    $profileType = $_POST['profile_type'];

    // Hash the password
    $hashedPassword = hashPassword($password);

    // Check if the email already exists
    $checkEmailSql = "SELECT * FROM User WHERE Email='$email'";
    $checkEmailResult = $conn->query($checkEmailSql);

    if ($checkEmailResult->num_rows > 0) {
        $error = "Error: Email already exists. Please use a different email.";
    } else {
        // Insert user into the database
        $sql = "INSERT INTO User (Username, Email, Password, Location, ProfileType) 
                VALUES ('$username', '$email', '$hashedPassword', '$location', '$profileType')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to login page or dashboard
            header("Location: login_user_rewear.php");
            exit;
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReWear - Register</title>
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

        /* Navbar Styles (Same as login page) */
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

        /* Registration Form Styles */
        .registration-container {
            max-width: 400px;
            margin: 120px auto 0;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .registration-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .registration-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .registration-form label {
            font-weight: 500;
        }

        .registration-form input, 
        .registration-form select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }

        .registration-form input:focus, 
        .registration-form select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .registration-form .submit-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 25px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .registration-form .submit-btn:hover {
            background: var(--secondary-color);
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .form-error {
            color: red;
            font-size: 0.9rem;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
    <script>
        // Password validation function
        function validatePassword() {
            var password = document.getElementById("password").value;
            var errorMessage = document.getElementById("password-error");

            // Password requirements: Minimum 8 characters, at least one uppercase letter, one number, and one special character
            var passwordPattern = /^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[@$!%?&])[A-Za-z\d@$!%?&]{8,}$/;

            if (!passwordPattern.test(password)) {
                errorMessage.textContent = "Password must be at least 8 characters long, contain one uppercase letter, one number, and one special character.";
                return false;
            } else {
                errorMessage.textContent = ""; // Clear error message
                return true;
            }
        }

        // Email validation function
        function validateEmail() {
            var email = document.getElementById("email").value;
            var errorMessage = document.getElementById("email-error");

            // Simple email format check using regex
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            
            if (!emailPattern.test(email)) {
                errorMessage.textContent = "Please enter a valid email address.";
                return false;
            } else {
                errorMessage.textContent = ""; // Clear error message
                return true;
            }
        }

        // Combined validation function for both email and password
        function validateForm() {
            return validatePassword() && validateEmail();
        }
    </script>
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

    <div class="registration-container">
        <h1>Register</h1>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form class="registration-form" action="register_user_rewear.php" method="POST" onsubmit="return validateForm()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <span id="email-error" class="form-error"></span>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <span id="password-error" class="form-error"></span>
            
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
            
            <label for="profile_type">Profile Type:</label>
            <select id="profile_type" name="profile_type" required>
                <option value="Buyer">Buyer</option>
                <option value="Seller">Seller</option>
            </select>
            
            <input type="submit" value="Register" class="submit-btn">
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login_user_rewear.php">Login here</a></p>
        </div>
    </div>
</body>
</html>