<?php
session_start();
require '../database/info.php'; // Include your database connection
require '../dashboard/header.php'; // Include your header connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user's inventory items
$query = "SELECT items.*, inventory.equipped 
          FROM inventory 
          JOIN items ON inventory.item_id = items.item_id 
          WHERE inventory.user_id = :user_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$inventoryItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Default avatar for all users
$defaultAvatar = "../dashboard/avatar/avatar.png"; // Update with your actual path
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Avatar Universe</title>
    <link rel="stylesheet" href="./css/global.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .avatar {
            width: 150px; /* Set your desired avatar size */
            height: 150px;
            background-image: url('<?php echo htmlspecialchars($defaultAvatar); ?>');
            background-size: cover;
            border-radius: 75px; /* Make it a circle */
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .inventory-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .item-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 200px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .item-card:hover {
            transform: scale(1.05);
        }

        .item-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .equip-button {
            margin-top: 10px;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .equip-button:hover {
            background-color: #0056b3;
        }

        .no-items {
            text-align: center;
            font-size: 18px;
            color: #777;
        }
    </style>
</head>
<body>
    <header>
        <h1>Your Inventory</h1>
    </header>

    <div class="avatar"></div> <!-- Avatar Display -->

    <div class="inventory-container">
        <?php if (empty($inventoryItems)): ?>
            <p class="no-items">No items in your inventory.</p>
        <?php else: ?>
            <?php foreach ($inventoryItems as $item): ?>
                <div class="item-card">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($item['price']); ?> Coins</p>
                    <form method="POST" action="equip.php">
                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                        <button type="submit" class="equip-button" <?php echo $item['equipped'] ? 'disabled' : ''; ?>>
                            <?php echo $item['equipped'] ? 'Equipped' : 'Equip'; ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
