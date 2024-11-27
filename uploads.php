<?php
include 'db_config.php'; // Include your database configuration file

if (isset($_POST['submit'])) {
    // Handling file upload
    $file = $_FILES['file'];
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];

    // File validation
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('jpg', 'jpeg', 'png');
    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 20000000) {
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = 'uploads/' . $fileNameNew;

                // Moving file to server
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Handling form fields
                    $itemName = $_POST['name'];
                    $description = $_POST['description'];
                    $price = $_POST['price'];
                    $category = $_POST['category'];
                    $conditions = $_POST['conditions'];
                    $size = $_POST['size'];
                    $color = $_POST['color'];
                    $swap_status = $_POST['swap_status'];

                    $brand = $_POST['brand'];
                    

                    // Insert item details into the database
                    $sql = "INSERT INTO items (name, description, price, category, `conditions`, size, color, swap_status, brand, image_path)
                            VALUES ('$itemName', '$description', '$price', '$category', '$conditions', '$size', '$color','$swap_status' ,'$brand', '$fileDestination')";

                    if ($conn->query($sql) === TRUE) {
                        echo "New record created successfully";
                        // Redirect after success
                        header("Location: sell.php?uploadsuccess");
                        exit();
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }

                } else {
                    echo "Failed to move uploaded file.";
                }
            } else {
                echo "Your file is too big";
            }
        } else {
            echo "There was an error uploading your file";
        }
    } else {
        echo "You cannot upload files of this type!";
    }
} else {
    echo "No file was uploaded.";
}

$conn->close();
?>
