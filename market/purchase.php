<?php
session_start();
require '../database/info.php';
require '../dashboard/header.php';// Include your database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); // Redirect to login page if not logged in
    exit;
}

// Check if item ID is set and is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid item ID.";
    exit;
}

$item_id = (int)$_GET['id'];

// Fetch item details
$stmt = $conn->prepare("SELECT * FROM items WHERE item_id = :item_id");
$stmt->bindParam(':item_id', $item_id);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the item exists
if (!$item) {
    echo "Item not found.";
    exit;
}

// Get user's coins
$user_id = $_SESSION['user_id'];
$user_stmt = $conn->prepare("SELECT coins FROM users WHERE id = :user_id");
$user_stmt->bindParam(':user_id', $user_id);
$user_stmt->execute();
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user has enough coins
if ($user['coins'] < $item['price']) {
    echo "Insufficient coins to purchase this item.";
    exit;
}

// Proceed with the purchase
try {
    // Deduct coins from user
    $new_coins = $user['coins'] - $item['price'];
    $update_user_stmt = $conn->prepare("UPDATE users SET coins = :new_coins WHERE id = :user_id");
    $update_user_stmt->bindParam(':new_coins', $new_coins);
    $update_user_stmt->bindParam(':user_id', $user_id);
    $update_user_stmt->execute();

    // Add item to user's inventory
    $add_to_inventory_stmt = $conn->prepare("INSERT INTO inventory (user_id, item_id) VALUES (:user_id, :item_id)");
    $add_to_inventory_stmt->bindParam(':user_id', $user_id);
    $add_to_inventory_stmt->bindParam(':item_id', $item_id);
    $add_to_inventory_stmt->execute();

    echo "Purchase successful! You now have " . $new_coins . " coins left.";
} catch (PDOException $e) {
    echo "Error during purchase: " . $e->getMessage();
}
?>
