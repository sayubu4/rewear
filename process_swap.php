<?php
session_start();
include 'db_config.php';

// Check if swap item data exists in session
if (!isset($_SESSION['swap_item'])) {
    header("Location: swap_form.php");
    exit();
}

try {
    // Prepare database insertion
    $sql = "INSERT INTO swap_items (
        name, category, condition_status, brand, size, color, 
        description, swap_preferences, images, user_id, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    // Encode images as JSON
    $images_json = json_encode($_SESSION['swap_item']['images']);
    
    // Use a placeholder user ID (replace with actual session user ID)
    $user_id = 1; 
    
    // Bind parameters
    $stmt->bind_param(
        "sssssssssi", 
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
    
    // Execute the statement
    if ($stmt->execute()) {
        // Clear the session data
        unset($_SESSION['swap_item']);
        
        // Redirect to a success page
        header("Location: swap_success.php");
        exit();
    } else {
        throw new Exception("Error saving item to database");
    }
    
} catch (Exception $e) {
    // Handle errors
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: swap_error.php");
    exit();
}

$conn->close();
?>
