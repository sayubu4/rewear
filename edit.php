<?php
session_start();

// If form data is submitted, store it in session
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['confirm_submission'])) {
    $_SESSION['swap_item'] = [
        'name' => $_POST['item_name'],
        'category' => $_POST['category'],
        'condition' => $_POST['condition'],
        'brand' => $_POST['brand'],
        'size' => $_POST['size'],
        'color' => $_POST['color'],
        'description' => $_POST['description'],
        'swap_preferences' => $_POST['swap_preferences']
    ];

    // Handle image uploads temporarily
    $uploadDir = 'temp_uploads/';
    $uploadedFiles = [];

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmp_name, $targetPath)) {
                $uploadedFiles[] = $targetPath;
            }
        }
        $_SESSION['swap_item']['images'] = $uploadedFiles;
    }
}

// If there's no session data, redirect back to the form
if (!isset($_SESSION['swap_item'])) {
    header("Location: swap_form.php");
    exit();
}

// Handle final submission
if (isset($_POST['confirm_submission'])) {
    include 'db_config.php';

    try {
        $sql = "INSERT INTO swap_items (name, category, condition_status, brand, size, color, 
                description, swap_preferences, images, user_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $images_json = json_encode($_SESSION['swap_item']['images']);
        $user_id = 1; // Replace with actual user ID from session

        $stmt->bind_param("sssssssssi", 
            $_SESSION['swap_item']['name'],
            $_SESSION['swap_item']['category'],
            $_SESSION['swap_item']['condition'],
            $_SESSION['swap_item']['brand'],
            $_SESSION['swap_item']['size'],
            $_SESSION['swap_item']['color'],
            $_SESSION['swap_item']['description'],
            $_SESSION['swap_item']['swap_preferences'],
            $images_json,
            $user_id
        );

        if ($stmt->execute()) {
            // Clear the session data
            unset($_SESSION['swap_item']);
            // Redirect to success page or dashboard
            header("Location: dashboard.php?success=1");
            exit();
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Your Swap Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url(2.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .review-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .review-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .review-section {
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
        }

        .review-label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .review-value {
            color: #444;
            margin-bottom: 15px;
        }

        .image-preview {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .confirm-button {
            background: #27ae60;
            color: white;
        }

        .edit-button {
            background: #3498db;
            color: white;
        }

        .button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="review-container">
        <div class="review-header">
            <h1>Review Your Item Details</h1>
            <p>Please review the information below before finalizing your listing</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="review-section">
            <div class="review-label">Item Name:</div>
            <div class="review-value"><?php echo htmlspecialchars($_SESSION['swap_item']['name']); ?></div>

            <div class="review-label">Category:</div>
            <div class="review-value"><?php echo htmlspecialchars($_SESSION['swap_item']['category']); ?></div>

            <div class="review-label">Condition:</div>
            <div class="review-value"><?php echo htmlspecialchars($_SESSION['swap_item']['condition']); ?></div>

            <div class="review-label">Brand:</div>
            <div class="review-value"><?php echo htmlspecialchars($_SESSION['swap_item']['brand']); ?></div>

            <div class="review-label">Size:</div>
            <div class="review-value"><?php echo htmlspecialchars($_SESSION['swap_item']['size']); ?></div>

            <div class="review-label">Color:</div>
            <div class="review-value"><?php echo htmlspecialchars($_SESSION['swap_item']['color']); ?></div>

            <div class="review-label">Description:</div>
            <div class="review-value"><?php echo nl2br(htmlspecialchars($_SESSION['swap_item']['description'])); ?></div>

            <div class="review-label">Swap Preferences:</div>
            <div class="review-value"><?php echo nl2br(htmlspecialchars($_SESSION['swap_item']['swap_preferences'])); ?></div>

            <?php if (isset($_SESSION['swap_item']['images'])): ?>
            <div class="review-label">Images:</div>
            <div class="image-preview">
                <?php foreach ($_SESSION['swap_item']['images'] as $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" class="preview-image" alt="Item image">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="button-group">
            <form action="swap_form.php" method="get">
                <input type="hidden" name="edit" value="1">
                <button type="submit" class="button edit-button">Edit Details</button>
            </form>
            <form action="swap_review.php" method="post">
                <button type="submit" name="confirm_submission" class="button confirm-button">Confirm & List Item</button>
            </form>
        </div>
    </div>
</body>
</html>