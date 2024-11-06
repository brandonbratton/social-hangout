<?php
// Start the session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include header
include '../dashboard/header.php'; // Make sure the path is correct

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Include database connection
require '../database/info.php';

$successMessage = '';
$errorMessage = '';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $itemName = trim($_POST['item_name']);
    $itemDescription = trim($_POST['item_description']);
    $itemCoins = intval($_POST['item_coins']);
    $itemGems = intval($_POST['item_gems']);
    $itemType = trim($_POST['item_type']);

    if (empty($itemName) || empty($itemDescription) || empty($itemType)) {
        $errorMessage = "All fields are required!";
    } elseif ($itemCoins < 0 && $itemGems < 0) {
        $errorMessage = "At least one price must be greater than or equal to zero!";
    } else {
        try {
            // Insert the item into the database
            $stmt = $conn->prepare("INSERT INTO items (name, description, coins, gems, type, user_id) VALUES (:name, :description, :coins, :gems, :type, :user_id)");
            $stmt->bindParam(':name', $itemName);
            $stmt->bindParam(':description', $itemDescription);
            $stmt->bindParam(':coins', $itemCoins);
            $stmt->bindParam(':gems', $itemGems);
            $stmt->bindParam(':type', $itemType);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();

            $successMessage = "Item added successfully!";
        } catch (PDOException $e) {
            $errorMessage = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item - Avatar Universe Marketplace</title>
    <link rel="stylesheet" href="./css/global.css">
</head>
<body>
    <header class="site-header">
        <h1>Add Item</h1>
    </header>

    <main>
        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="text" name="item_name" placeholder="Item Name" required>
            <textarea name="item_description" placeholder="Item Description" required></textarea>
            
            <div>
                <input type="number" name="item_coins" placeholder="Price in Coins" min="0">
                <input type="number" name="item_gems" placeholder="Price in Gems" min="0">
            </div>
            
            <select name="item_type" required>
                <option value="">Select Item Type</option>
                <option value="hat">Hat</option>
                <option value="shirt">Shirt</option>
                <option value="pants">Pants</option>
                <option value="shoes">Shoes</option>
                <option value="accessory">Gear</option>
                <!-- Add more options as needed -->
            </select>
            <button type="submit">Create Item</button>
        </form>
    </main>
</body>
</html>
