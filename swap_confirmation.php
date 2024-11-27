<?php
// swap_confirmation.php
session_start();

if (!isset($_SESSION['last_submitted_item'])) {
    header("Location: swap_form.php");
    exit();
}

$item = $_SESSION['last_submitted_item'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swap Item Listed Successfully</title>
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #2ecc71;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }

        .confirmation-title {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .confirmation-message {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .item-preview {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .item-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .button {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .button:hover {
            transform: translateY(-2px);
        }

        .primary-button {
            background: #3498db;
            color: white;
        }

        .secondary-button {
            background: #e9ecef;
            color: #2c3e50;
        }

        .next-steps {
            margin-top: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .next-steps h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .next-steps ul {
            text-align: left;
            margin: 0;
            padding-left: 20px;
        }

        .next-steps li {
            margin-bottom: 10px;
            color: #666;
        }

        @media (max-width: 768px) {
            .confirmation-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        
        <h1 class="confirmation-title">Item Successfully Listed!</h1>
        <p class="confirmation-message">Your item "<?php echo htmlspecialchars($item['name']); ?>" has been listed for swapping.</p>

        <div class="item-preview">
            <?php if (!empty($item['images'])): ?>
                <img src="<?php echo htmlspecialchars($item['images'][0]); ?>" alt="Item Preview" class="item-image">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
            <p>Category: <?php echo htmlspecialchars($item['category']); ?></p>
        </div>

        <div class="next-steps">
            <h3>What's Next?</h3>
            <ul>
                <li>Your item is now visible to other users looking to swap</li>
                <li>You'll receive notifications when someone is interested in your item</li>
                <li>Browse other items to find potential matches</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="browse_items.php" class="button primary-button">Browse Items to Swap</a>
            <a href="my_items.php" class="button secondary-button">View My Items</a>
            <a href="swap_form.php" class="button secondary-button">List Another Item</a>
        </div>
    </div>

    <?php
    // Clear the session data after displaying
    unset($_SESSION['last_submitted_item']);
    unset($_SESSION['success_message']);
    ?>
</body>
</html>