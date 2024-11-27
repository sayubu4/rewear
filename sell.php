<?php
include 'db_config.php';
include 'session_check.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sell Clothes</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* General Styles */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: url(lena.jpg) no-repeat center center fixed; /* Add your image URL */
      background-size: cover;
      color: black;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.9); /* Transparent white background */
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h1 {
      text-align: center;
      color: black;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select,
    input[type="file"],
    button {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-sizing: border-box;
      font-size: 14px;
    }

    textarea {
      resize: vertical;
      height: 100px;
    }

    button {
      background-color: black;
      color: #fff;
      border: none;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #218c4f;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        margin: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Sell an Item!</h1>
    <form action="uploads.php" method="POST" enctype="multipart/form-data">
      <label for="name">Item Name:</label>
      <input type="text" id="name" name="name" placeholder="Enter item name" required>

      <label for="description">Description:</label>
      <textarea id="description" name="description" placeholder="Provide a brief description" required></textarea>

      <label for="price">Price ($):</label>
      <input type="number" id="price" name="price" placeholder="Enter the price" required>

      <label for="category">Category:</label>
      <select name="category" id="category">
        <option value="men">Men</option>
        <option value="women">Women</option>
        <option value="kids">Kids</option>
        <option value="accessories">Accessories</option>
      </select>

      <label for="conditions">Condition:</label>
      <select name="conditions" id="conditions">
        <option value="new">New</option>
        <option value="lightly_used">Lightly Used</option>
        <option value="worn">Worn</option>
      </select>

      <label for="size">Size:</label>
      <input type="text" id="size" name="size" placeholder="E.g., M, L, XL" required>

      <label for="color">Color:</label>
      <input type="text" id="color" name="color" placeholder="Enter color" required>

      <label for="swap_status">Listing Type:</label>
      <select name="swap_status" id="swap_status">
          <option value="sell">Sell Only</option>
          <option value="swap">Swap Only</option>
          <option value="both">Sell or Swap</option>
      </select>

      <label for="brand">Brand:</label>
      <input type="text" id="brand" name="brand" placeholder="Enter brand name" required>

      <label for="file">Upload Image:</label>
      <input type="file" name="file" required>

      <button type="submit" name="submit">Upload Item</button>
    </form>
  </div>
</body>
</html>
