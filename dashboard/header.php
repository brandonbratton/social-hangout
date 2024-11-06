<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require '../database/info.php';

// Fetch user details for the dropdown (you can fetch username and currency)
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, coins, gems, role FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Dashboard</title>
    <link rel="stylesheet" href="../css/global.css"> <!-- Global CSS for consistent styles -->
    <link rel="stylesheet" href="../css/navbar.css"> <!-- Custom Navbar Styles -->
</head>
<body>
    <div class="navbar">
        <div class="navbar-header">
            <h2 class="navbar-title">Avatar Universe</h2>
        </div>
        <ul class="navbar-menu">
            <li><a href="../dashboard/index.php">My Wall</a></li>
             <li><a href="../dashboard/inventory.php">My Inventory</a></li>
                          <li><a href="../chatrooms/index.php">Socialize</a></li>
            <li><a href="../users/viewusers.php">View Users</a></li>
            <li><a href="../market/index.php">Marketplace</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn"><?php echo htmlspecialchars($user['username']); ?> ▼</a>
                <div class="dropdown-content">
            <a href="#">Coins: <span id="user-coins"><?php echo htmlspecialchars($user['coins']); ?></span></a>
            <a href="#">Gems: <span id="user-gems"><?php echo htmlspecialchars($user['gems']); ?></span></a>
                    <a href="../dashboard/settings.php">Settings</a>
                    <a href="../inbox/index.php">Inbox</a>
                    <a href="../dashboard/friend_requests.php">Friend Requests</a>
                    <?php if ($user['role'] === 'admin' || $user['role'] === 'moderator'): ?>
                        <a href="../admin/index.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="./logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <!-- Main content will be added to this section -->
