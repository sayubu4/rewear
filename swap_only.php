<?php
include 'db_config.php';

// Determine the specific filter
$swap_status = isset($_GET['status']) ? $_GET['status'] : 'swap_only';

// Prepare the query based on exact swap status
if ($swap_status == 'swap_only') {
    $query = "SELECT * FROM items WHERE swap_status = 'swap'";
} elseif ($swap_status == 'sell_or_swap') {
    $query = "SELECT * FROM items WHERE swap_status = 'both'";
} else {
    $query = "SELECT * FROM items WHERE swap_status IN ('swap', 'both')";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swap Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url(lena.jpg) no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .item-card {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .item-card img {
            max-width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }
        .filter-section {
            margin-bottom: 20px;
            text-align: center;
        }
        .filter-section a {
            margin: 0 10px;
            text-decoration: none;
            color: black;
            padding: 5px 10px;
            border: 1px solid black;
            border-radius: 5px;
        }
        .filter-section a.active {
            background-color: black;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center;">Swap Items</h1>
        
        <div class="filter-section">
            <a href="swap.php?status=swap_only" class="<?= $swap_status == 'swap_only' ? 'active' : '' ?>">Swap Only</a>
            <a href="swap.php?status=sell_or_swap" class="<?= $swap_status == 'sell_or_swap' ? 'active' : '' ?>">Sell or Swap</a>
        </div>

        <div class="item-grid">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <div class="item-card">
                    <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p>Brand: <?= htmlspecialchars($row['brand']) ?></p>
                    <p>Size: <?= htmlspecialchars($row['size']) ?></p>
                    <p>Condition: <?= htmlspecialchars($row['conditions']) ?></p>
                    <p>Swap Status: <?= htmlspecialchars($row['swap_status']) ?></p>
                    <?php if ($row['swap_status'] == 'both') { ?>
                        <p>Price: $<?= htmlspecialchars($row['price']) ?></p>
                    <?php } ?>
                </div>
            <?php 
                }
            } else {
                echo "<p style='text-align: center; width: 100%;'>No items available for the selected swap type.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>