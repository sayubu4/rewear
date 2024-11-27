<?php
session_start();

// Include database configuration
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_user_rewear.php');
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User'; // Fallback to 'User' if username not set

// Fetch user's profile information
$user_sql = "SELECT Username, Location, Rating, VerifiedStatus FROM User WHERE UserID = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Your existing queries for listed items and swap requests remain the same
// ... (keep your existing queries)

?>

<!-- In your HTML, update the welcome message -->
<div class="welcome-section">
    <div class="profile-image">
        <i class="fas fa-user"></i>
    </div>
    <div>
        <h2>Welcome back, <?php echo htmlspecialchars($user_data) ?>!</h2>
        <p>
            <?php if ($user_data['VerifiedStatus']): ?>
                <i class="fas fa-check-circle" style="color: var(--secondary-color);"></i> Verified User
            <?php endif; ?>
            <?php if ($user_data['Location']): ?>
                <span class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($user_data['Location']); ?></span>
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Add a logout button in your dashboard header -->
<div class="dashboard-header">
    <!-- ... existing welcome section ... -->
    <div class="header-actions">
        <a href="swap_form.php" class="action-button add-item-button">
            <i class="fas fa-plus"></i> List New Item
        </a>
        <a href="logout.php" class="action-button">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>