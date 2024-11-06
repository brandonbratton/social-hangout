<?php
session_start();
require '../database/info.php'; // Include your database connection
require '../dashboard/header.php'; // Include your header connection

// Check if an item ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid item ID');
}

$item_id = $_GET['id'];

// Fetch item details from the database
$stmt = $conn->prepare("SELECT * FROM items WHERE item_id = :item_id");
$stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die('Item not found');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['name']); ?> - Item Details</title>
    <link rel="stylesheet" href="../css/global.css">
    <style>
        .item-details {
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa; /* Light background for contrast */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .item-details img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px; /* Space between image and text */
        }

        .purchase-button {
            padding: 10px 20px;
            background-color: #28a745; /* Green color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .purchase-button:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff; /* Blue color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>
<body>
    <div class="item-details">
        <h1><?php echo htmlspecialchars($item['name']); ?></h1>
        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
        <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
        <p>Price: <?php echo htmlspecialchars($item['price']); ?> Coins</p>

        <?php if (isset($_SESSION['user_id'])): // Check if user is logged in ?>
            <form action="purchase.php" method="POST">
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                <button type="submit" class="purchase-button">Purchase</button>
            </form>
        <?php else: ?>
            <p>Please <a href="../login.php">log in</a> to purchase this item.</p>
        <?php endif; ?>

        <a href="index.php" class="back-button">Back to Marketplace</a>
    </div>
</body>
</html>
