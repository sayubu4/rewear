<?php
// browse_items.php - For browsing available swap items
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Swap Items</title>
    <style>
        .browse-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-group select, .filter-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .item-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .item-card:hover {
            transform: translateY(-4px);
        }

        .item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .item-info {
            padding: 15px;
        }

        .item-title {
            font-size: 1.2rem;
            margin: 0 0 10px 0;
        }

        .item-category {
            color: #666;
            font-size: 0.9rem;
        }

        .item-condition {
            display: inline-block;
            padding: 4px 8px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-top: 8px;
        }

        .swap-button {
            display: block;
            width: 100%;
            padding: 10px;
            background: #3498db;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
            transition: background-color 0.2s ease;
        }

        .swap-button:hover {
            background: #2980b9;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }

        .pagination a.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        @media (max-width: 768px) {
            .browse-container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="browse-container">
        <div class="filters">
            <h2>Filter Items</h2>
            <form class="filter-grid">
                <div class="filter-group">
                    <select name="category">
                        <option value="">All Categories</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Books">Books</option>
                        <option value="Sports">Sports Equipment</option>
                        <option value="Home">Home & Garden</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="condition">
                        <option value="">Any Condition</option>
                        <option value="New">New</option>
                        <option value="Like New">Like New</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                    </select>
                </div>
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Search items...">
                </div>
            </form>
        </div>

        <div class="items-grid">
            <?php
            include 'db_config.php';
            
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $items_per_page = 12;
            $offset = ($page - 1) * $items_per_page;
            
            // Add filters to query if set
            $where_clauses = [];
            $params = [];
            $types = "";
            
            if (!empty($_GET['category'])) {
                $where_clauses[] = "category = ?";
                $params[] = $_GET['category'];
                $types .= "s";
            }
            
            if (!empty($_GET['condition'])) {
                $where_clauses[] = "condition_status = ?";
                $params[] = $_GET['condition'];
                $types .= "s";
            }
            
            if (!empty($_GET['search'])) {
                $where_clauses[] = "(name LIKE ? OR description LIKE ?)";
                $search_term = "%" . $_GET['search'] . "%";
                $params[] = $search_term;
                $params[] = $search_term;
                $types .= "ss";
            }
            
            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
            
            $sql = "SELECT * FROM swap_items $where_sql ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $types .= "ii";
            $params[] = $items_per_page;
            $params[] = $offset;
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($item = $result->fetch_assoc()):
                $images = json_decode($item['images'], true);
                $first_image = !empty($images) ? $images[0] : 'placeholder.jpg';
            ?>
            <div class="item-card">
                <img src="<?php echo htmlspecialchars($first_image); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                <div class="item-info">
                    <h3 class="item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                    <div class="item-category"><?php echo htmlspecialchars($item['category']); ?></div>
                    <div class="item-condition"><?php echo htmlspecialchars($item['condition_status']); ?></div>
                    <a href="item_details.php?id=<?php echo $item['id']; ?>" class="swap-button">View Details</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php
        // Calculate total pages for pagination
        $total_sql = "SELECT COUNT(*) as count FROM swap_items $where_sql";
        $stmt = $conn->prepare($total_sql);
        if (!empty($params)) {
            array_pop($params); // Remove LIMIT
            array_pop($params); // Remove OFFSET
            $stmt->bind_param(substr($types, 0, -2), ...$params);
        }
        $stmt->execute();
        $total_result = $stmt->get_result();
        $total_items = $total_result->fetch_assoc()['count'];
        $total_pages = ceil($total_items / $items_per_page);
        ?>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo ($page === $i) ? 'class="active"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>

