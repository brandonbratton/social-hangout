<?php
session_start(); // Start the session if not already started

// Include database connection
require '../database/info.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch pending friend requests
$requestsStmt = $conn->prepare("SELECT fr.id, u.username AS sender 
                                 FROM friend_requests fr 
                                 JOIN users u ON fr.sender_id = u.id 
                                 WHERE fr.recipient_id = :user_id");
$requestsStmt->bindParam(':user_id', $userId);
$requestsStmt->execute();
$requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle accepting friend requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_request'])) {
    $requestId = intval($_POST['request_id']);
    
    try {
        // Fetch the sender ID from the request
        $fetchRequestStmt = $conn->prepare("SELECT sender_id FROM friend_requests WHERE id = :request_id");
        $fetchRequestStmt->bindParam(':request_id', $requestId);
        $fetchRequestStmt->execute();
        $request = $fetchRequestStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Create friendship
            $senderId = $request['sender_id'];
            $insertFriendshipStmt = $conn->prepare("INSERT INTO friendships (user_id, friend_id) VALUES (:user_id, :friend_id)");
            $insertFriendshipStmt->bindParam(':user_id', $userId);
            $insertFriendshipStmt->bindParam(':friend_id', $senderId);
            $insertFriendshipStmt->execute();

            // Delete the request
            $deleteRequestStmt = $conn->prepare("DELETE FROM friend_requests WHERE id = :request_id");
            $deleteRequestStmt->bindParam(':request_id', $requestId);
            $deleteRequestStmt->execute();
        }
    } catch (PDOException $e) {
        echo "Error accepting request: " . $e->getMessage();
    }
}

// Handle declining friend requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decline_request'])) {
    $requestId = intval($_POST['request_id']);
    
    try {
        $deleteRequestStmt = $conn->prepare("DELETE FROM friend_requests WHERE id = :request_id");
        $deleteRequestStmt->bindParam(':request_id', $requestId);
        $deleteRequestStmt->execute();
    } catch (PDOException $e) {
        echo "Error declining request: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friend Requests</title>
    <link rel="stylesheet" href="../css/global.css">
</head>

 <?php include 'header.php'; ?>
<body>
    <header>
        <h1>Your Friend Requests</h1>
    </header>
    <section>
        <h2>Pending Friend Requests</h2>
        <ul>
            <?php if (count($requests) > 0): ?>
                <?php foreach ($requests as $request): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($request['sender']); ?></strong>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" name="accept_request">Accept</button>
                            <button type="submit" name="decline_request">Decline</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No pending friend requests.</li>
            <?php endif; ?>
        </ul>
    </section>

    <footer>
        <a href="../index.php">Back to Homepage</a>
    </footer>
</body>
</html>
