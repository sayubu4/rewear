<?php
// process_swap_form.php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_config.php';
    
    try {
        // Your existing form processing code here
        
        if ($stmt->execute()) {
            $item_id = $conn->insert_id; // Get the ID of the newly inserted item
            $_SESSION['success_message'] = "Your item has been successfully listed for swap!";
            
            // Store item details in session for confirmation page
            $_SESSION['last_submitted_item'] = [
                'id' => $item_id,
                'name' => $name,
                'category' => $category,
                'images' => $uploadedFiles
            ];
            
            // Redirect to success page
            header("Location: swap_confirmation.php");
            exit();
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: swap_form.php");
        exit();
    }
    
    $conn->close();
}
?>

