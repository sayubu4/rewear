<?php
include 'session_check.php';
include 'db_config.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch current user details
$stmt = $conn->prepare("SELECT * FROM user WHERE UserID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $new_location = $_POST['location'];

    // Update user profile
    $update_stmt = $conn->prepare("UPDATE user SET username = ?, location = ? WHERE UserID = ?");
    $update_stmt->bind_param("ssi", $new_username, $new_location, $user_id);
    
    if ($update_stmt->execute()) {
        // Update session username if changed
        $_SESSION['username'] = $new_username;
        header("Location: dashboard.php?profile_update=1");
        exit();
    } else {
        $error_message = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .profile-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="profile-form">
        <h2>Edit Profile</h2>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" placeholder="Enter your location">
            </div>
            <button type="submit" class="submit-btn">Update Profile</button>
            <a href="dashboard.php" style="display: block; text-align: center; margin-top: 15px; color: #3498db;">Cancel</a>
        </form>
    </div>
</body>
</html>