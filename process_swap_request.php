<?php
session_start();
include 'db_config.php';
include 'session_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $current_user_id = $_SESSION['user_id'];

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Verify the request belongs to the current user
        $verify_query = "SELECT si.user_id 
                         FROM swap_requests sr
                         JOIN swap_items si ON sr.requested_item_id = si.id
                         WHERE sr.id = ?";
        $stmt_verify = $conn->prepare($verify_query);
        $stmt_verify->bind_param("i", $request_id);
        $stmt_verify->execute();
        $result = $stmt_verify->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception("Invalid request");
        }
        
        $row = $result->fetch_assoc();
        if ($row['user_id'] != $current_user_id) {
            throw new Exception("Unauthorized action");
        }

        // Update the swap request status
        $update_query = "UPDATE swap_requests 
                         SET status = ?, 
                             updated_at = NOW() 
                         WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        
        // Determine the new status based on action
        $new_status = ($action == 'accept') ? 'accepted' : 'rejected';
        $stmt_update->bind_param("si", $new_status, $request_id);
        $stmt_update->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the notifications page
        header("Location: notifications.php?success=1");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        
        // Redirect back with error
        header("Location: notifications.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Redirect if accessed incorrectly
    header("Location: notifications.php");
    exit();
}