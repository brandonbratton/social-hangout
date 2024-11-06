<?php
session_start();
require '../database/info.php'; // Include your database connection
require '../dashboard/header.php'; // Include your header connection

// Handle category filtering
$category = isset($_GET['category']) ? $_GET['category'] : '';
$query = "SELECT * FROM items";
if ($category) {
    $query .= " WHERE category = :category"; // Filter by category if set
}
$stmt = $conn->prepare($query);
if ($category) {
    $stmt->bindParam(':category', $category);
}
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Avatar Universe</title>
    <link rel="stylesheet" href="./css/global.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            max-width: 1200px; /* Limit the maximum width of the entire page */
            margin: 0 auto; /* Center the page */
            padding: 20px; /* Add some padding around the content */
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        .add-item-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745; /* Green color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }

        .add-item-button:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .container {
            display: flex;
            gap: 20px; /* Space between sidebar and main content */
        }

        aside {
            flex: 1; /* Allow sidebar to take up one part of the space */
            min-width: 200px; /* Ensure a minimum width for the sidebar */
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        aside h2 {
            margin-bottom: 10px;
        }

        aside ul {
            list-style-type: none;
            padding: 0;
        }

        aside ul li {
            margin-bottom: 8px;
        }

        aside ul li a {
            text-decoration: none;
            color: #007BFF;
            transition: color 0.3s;
        }

        aside ul li a:hover {
            color: #0056b3; /* Darker shade on hover */
        }

        main {
            flex: 3; /* Allow main content to take up three parts of the space */
        }

        .item-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .item-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 200px;
            text-align: center;
            cursor: pointer; /* Change cursor to pointer for clickable items */
            transition: transform 0.3s; /* Add a slight scaling effect */
        }

        .item-card:hover {
            transform: scale(1.05); /* Scale up on hover */
        }

        .item-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .buy-button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .buy-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Marketplace</h1>
        <a href="add_item.php" class="add-item-button">Add New Item</a>
    </header>

    <div class="container">
        <aside>
            <h2>Filter by Category</h2>
            <ul>
                <li><a href="?category=hats">Hats</a></li>
                <li><a href="?category=shirts">Shirts</a></li>
                <li><a href="?category=pants">Pants</a></li>
                <li><a href="?category=shoes">Shoes</a></li>
                <li><a href="?category=accessories">Accessories</a></li>
            </ul>
        </aside>

        <main>
            <h2>Items for Sale</h2>

            <?php if (empty($items)): ?>
                <p>No items available in this category.</p>
            <?php else: ?>
                <div class="item-container">
                    <?php foreach ($items as $item): ?>
                        <div class="item-card" onclick="window.location.href='item.php?id=<?php echo $item['item_id']; ?>'">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <p>Price: <?php echo htmlspecialchars($item['price']); ?> Coins</p> <!-- Corrected the price field -->
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

