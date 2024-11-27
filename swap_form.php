<?php
// swap_form.php
include 'session_check.php';
include 'db_config.php';

// Initialize error/success message variables
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    
    try {
        // Validate and process the form data
        $name = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
        $condition = filter_input(INPUT_POST, 'condition', FILTER_SANITIZE_STRING);
        $brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
        $size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
        $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $swap_preferences = filter_input(INPUT_POST, 'swap_preferences', FILTER_SANITIZE_STRING);
        
        // Handle image upload
        $uploadDir = 'uploads/';
        $uploadedFiles = [];
        
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = uniqid() . '_' . $_FILES['images']['name'][$key];
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $uploadedFiles[] = $targetPath;
                }
            }
        }
        
        // Insert into database (you'll need to create this table)
        $sql = "INSERT INTO swap_items (name, category, condition_status, brand, size, color, 
                description, swap_preferences, images, user_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $images_json = json_encode($uploadedFiles);
        $user_id =$_SESSION['user_id'];
        
        $stmt->bind_param("sssssssssi", $name, $category, $condition, $brand, $size, $color, 
                         $description, $swap_preferences, $images_json, $user_id);
        
        if ($stmt->execute()) {
            $message = "Your item has been successfully listed for swap!";
            $messageType = "success";
        } else {
            throw new Exception("Error saving item to database");
        }
        
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Item for Swap</title>
    <style>
        <style>
        /* Apply a background image */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url(2.jpg); /* Replace with your image URL */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .swap-form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h1 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-weight: 600;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            border-color: #3498db;
            outline: none;
        }

        .image-upload {
            border: 2px dashed #e0e0e0;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .image-upload:hover {
            border-color: #3498db;
        }

        .submit-button {
            background: #3498db;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }

        .submit-button:hover {
            background: #2980b9;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .swap-form-container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="swap-form-container">
        <div class="form-header">
            <h1>List Your Item for Swap</h1>
            <p>Share details about your item and what you'd like to swap it for</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form  method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="item_name">Item Name</label>
                    <input type="text" id="item_name" name="item_name" required>
                </div>


                <div class="form-group">
                    <label for="condition">Condition</label>
                    <select id="condition" name="condition" required>
                        <option value="">Select Condition</option>
                        <option value="New">New</option>
                        <option value="Like New">Like New</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Poor">Poor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand">
                </div>

                <div class="form-group">
                    <label for="size">Size</label>
                    <input type="text" id="size" name="size">
                </div>

                <div class="form-group">
                    <label for="color">Color</label>
                    <input type="text" id="color" name="color">
                </div>

                <div class="form-group full-width">
                    <label for="description">Item Description</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="swap_preferences">What would you like to swap for?</label>
                    <textarea id="swap_preferences" name="swap_preferences" rows="4" required 
                        placeholder="Describe what items you're interested in swapping for..."></textarea>
                </div>

                <div class="form-group full-width">
                    <div class="image-upload">
                        <label for="images">Upload Images (Max 5 images)</label>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" 
                            onchange="previewImages(this)" required>
                        <div class="image-preview" id="imagePreview"></div>
                    </div>
                </div>
            </div>

            
            <button type="submit" class="submit-button">List Item for Swap</button>
        </form>
    </div>

    <script>
        function previewImages(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';

            if (input.files) {
                const files = Array.from(input.files).slice(0, 5); // Limit to 5 images
                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'preview-image';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
</body>
</html>