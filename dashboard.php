<?php
// dashboard.php
include 'session_check.php';
// Include database configuration
include 'db_config.php';

// Fetch user's listed items
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id']; // Replace with actual user ID from session
$listed_items_sql = "SELECT * FROM swap_items WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($listed_items_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$listed_items_result = $stmt->get_result();
$listed_items = $listed_items_result->fetch_all(MYSQLI_ASSOC);

echo implode(", ", $listed_items);

// Fetch incoming swap requests (you'll need to create this table)
$incoming_requests_sql = "SELECT sr.*, si.name as item_name, si.images 
                         FROM swap_requests sr 
                         JOIN swap_items si ON sr.requested_item_id = si.id 
                         WHERE si.user_id = ? AND sr.status = 'pending'";
$stmt = $conn->prepare($incoming_requests_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$incoming_requests_result = $stmt->get_result();
$incoming_requests = $incoming_requests_result->fetch_all(MYSQLI_ASSOC);

// Check for success message
// not n
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Your item has been successfully listed!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewear Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #f5f6fa;
            --card-color: #ffffff;
            --text-color: #2c3e50;
            --border-radius: 15px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--card-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .welcome-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-image i {
            font-size: 30px;
            color: #666;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-color);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            text-align: center;
        }

        .stat-card i {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--text-color);
            margin: 10px 0;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .section {
            background: var(--card-color);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .section-title {
            font-size: 1.2em;
            font-weight: bold;
            color: var(--text-color);
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .item-card {
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .item-card:hover {
            transform: translateY(-5px);
        }

        .item-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .item-details {
            padding: 15px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .item-category {
            color: #666;
            font-size: 0.9em;
        }

        .request-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }

        .request-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
        }

        .request-details {
            flex-grow: 1;
        }

        .request-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .request-user {
            font-size: 0.9em;
            color: #666;
        }

        .success-message {
            background: var(--secondary-color);
            color: white;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            text-align: center;
        }

        .action-button {
            background: var(--primary-color);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }

        .action-button:hover {
            background: #2980b9;
        }

        .add-item-button {
            background: var(--secondary-color);
            padding: 12px 24px;
            font-size: 1.1em;
        }

        .add-item-button:hover {
            background: #27ae60;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-header">
            <div class="welcome-section">
                <div class="profile-image">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h2>Welcome back, <?php echo " $username "?>!!</h2>
                    <p>Manage your items and swap requests</p>
                </div>
            </div>
            <a href="swap_form.php" class="action-button add-item-button">
                <i class="fas fa-plus"></i> List New Item
            </a>

            <a href="edit_profile.php" class="action-button" style="margin-left: 10px; background-color: #f39c12;">
                <i class="fas fa-user-edit"></i> Edit Profile
            </a>

            <a href="logout.php" class="action-button" style="margin-left: 10px;">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </a>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <div class="stat-value"><?php echo count($listed_items); ?></div>
                <div class="stat-label">Listed Items</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-exchange-alt"></i>
                <div class="stat-value"><?php echo count($incoming_requests); ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <div class="stat-value">0</div>
                <div class="stat-label">Completed Swaps</div>
            </div>
        </div>

        <div class="content-grid">
            <div class="section">
                <div class="section-header">
                    <h3 class="section-title">Your Listed Items</h3>
                    <a href="#" class="action-button">View All</a>
                </div>
                <div class="items-grid">
                    <?php foreach ($listed_items as $item): 
                        $images = json_decode($item['images'], true);
                        $first_image = !empty($images) ? $images[0] : 'placeholder.jpg';
                        $name = $item['name'];
                        $category = $item['category'];
                    ?>
                  <div class="item-card">
                            <img src="<?php echo htmlspecialchars($first_image ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name'] ?? 'Unnamed Item'); ?>" class="item-image">
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($name ?? 'Unnamed Item'); ?></div>
                                <div class="item-category"><?php echo htmlspecialchars($category ?? 'Uncategorized'); ?></div>
                            </div>
               </div>

                    <?php endforeach; ?>
                </div>
            </div>

            <div class="section">
                <div class="section-header">
                    <h3 class="section-title">Incoming Swap Requests</h3>
                </div>
                <?php if (empty($incoming_requests)): ?>
                    <p style="text-align: center; color: #666;">No pending swap requests</p>
                <?php else: ?>
                    <?php foreach ($incoming_requests as $request): 
                        $images = json_decode($request['images'], true);
                        $first_image = !empty($images) ? $images[0] : 'placeholder.jpg';
                    ?>
                        <div class="request-card">
                            <img src="<?php echo htmlspecialchars($first_image); ?>" alt="<?php echo htmlspecialchars($request['item_name']); ?>" class="request-image">
                            <div class="request-details">
                                <div class="request-title"><?php echo htmlspecialchars($request['item_name']); ?></div>
                                <div class="request-user">From: User #<?php echo htmlspecialchars($request['user_id']); ?></div>
                            </div>
                            <button class="action-button">View</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Add any interactive features here
        document.addEventListener('DOMContentLoaded', function() {
            // Handle success message dismissal
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.opacity = '0';
                    setTimeout(() => {
                        successMessage.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>