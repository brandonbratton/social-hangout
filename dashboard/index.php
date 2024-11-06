<?php
session_start();
// Start the session

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require '../database/info.php';

// Fetch user's details
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user's friends
$friendsStmt = $conn->prepare("SELECT u.username FROM friendships f JOIN users u ON f.friend_id = u.id WHERE f.user_id = :user_id");
$friendsStmt->bindParam(':user_id', $userId);
$friendsStmt->execute();
$friends = $friendsStmt->fetchAll(PDO::FETCH_ASSOC);

// Update status logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = trim($_POST['status']);
    if (!empty($status)) {
        $updateStatusStmt = $conn->prepare("INSERT INTO user_statuses (user_id, status) VALUES (:user_id, :status)");
        $updateStatusStmt->bindParam(':user_id', $userId);
        $updateStatusStmt->bindParam(':status', $status);
        $updateStatusStmt->execute();
    }
}

// Fetch user's latest status
$statusStmt = $conn->prepare("SELECT status FROM user_statuses WHERE user_id = :user_id ORDER BY updated_at DESC LIMIT 1");
$statusStmt->bindParam(':user_id', $userId);
$statusStmt->execute();
$currentStatus = $statusStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Dashboard</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    </header>
    
     <?php include './header.php'; ?>
    
    <main>
        <section id="statusUpdate">
            <h2>Update Your Status</h2>
            <form method="POST" action="">
                <input type="text" name="status" placeholder="What's on your mind?" required>
                <button type="submit">Update Status</button>
            </form>
            <?php if ($currentStatus): ?>
                <h3>Your Current Status:</h3>
                <p><?php echo htmlspecialchars($currentStatus); ?></p>
            <?php endif; ?>
        </section>
        
        <section id="friendsList">
            <h2>Your Friends</h2>
            <ul>
                <?php if (count($friends) > 0): ?>
                    <?php foreach ($friends as $friend): ?>
                        <li><?php echo htmlspecialchars($friend['username']); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No friends added yet.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section id="avatarDisplay">
            <h2>Your Avatar</h2>
            <img src="path/to/avatar_images/<?php echo $userId; ?>.png" alt="Your Avatar" class="avatar-image">
        </section>
        
<!-- Place this button wherever you'd like in your dashboard -->
<a href="./logout.php" class="logout-button">Log Out</a>

        
    </main>
</body>
</html>
