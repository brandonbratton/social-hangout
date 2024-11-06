<?php
// Start the session
session_start();

// Include database connection
require '../database/info.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if ID is set in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = $_GET['id'];
    
    // Fetch user's details
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch user's friends
    $friendsStmt = $conn->prepare("SELECT u.username FROM friendships f JOIN users u ON f.friend_id = u.id WHERE f.user_id = :user_id");
    $friendsStmt->bindParam(':user_id', $userId);
    $friendsStmt->execute();
    $friends = $friendsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle sending friend requests
    if (isset($_POST['send_friend_request']) && isset($_SESSION['user_id'])) {
        $senderId = $_SESSION['user_id'];
        
        // Check if already friends or if the request already exists
        $checkStmt = $conn->prepare("SELECT * FROM friendships WHERE user_id = :user_id AND friend_id = :friend_id");
        $checkStmt->bindParam(':user_id', $senderId);
        $checkStmt->bindParam(':friend_id', $userId);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            $error = "You are already friends with this user!";
        } else {
            $friendRequestStmt = $conn->prepare("INSERT INTO friend_requests (sender_id, recipient_id) VALUES (:sender_id, :recipient_id)");
            $friendRequestStmt->bindParam(':sender_id', $senderId);
            $friendRequestStmt->bindParam(':recipient_id', $userId);
            $friendRequestStmt->execute();
            $success = "Friend request sent successfully!";
        }
    }

    if (!$user) {
        // If user not found, redirect to a 404 or display a message
        echo "User not found!";
        exit();
    }
} else {
    echo "Invalid user ID!";
    exit();
}

// Fetch pending friend requests for the logged-in user
$loggedInUserId = $_SESSION['user_id'];
$requestsStmt = $conn->prepare("SELECT u.username FROM friend_requests fr JOIN users u ON fr.sender_id = u.id WHERE fr.recipient_id = :recipient_id");
$requestsStmt->bindParam(':recipient_id', $loggedInUserId);
$requestsStmt->execute();
$requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>
    </header>
    
    <section id="profileDetails">
        <h2>User Details</h2>
        <img src="../avatar_images/<?php echo $userId; ?>.png" alt="Avatar of <?php echo htmlspecialchars($user['username']); ?>" class="avatar-image">
        <p></p>
        
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $userId): ?>
            <form method="POST" action="">
                <button type="submit" name="send_friend_request">Send Friend Request</button>
            </form>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (isset($success)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
    </section>
    
    <section id="friendsList">
        <h2><?php echo htmlspecialchars($user['username']); ?>'s Friends</h2>
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

    <section id="friendRequests">
        <h2>Your Pending Friend Requests</h2>
        <ul>
            <?php if (count($requests) > 0): ?>
                <?php foreach ($requests as $request): ?>
                    <li>
                        <?php echo htmlspecialchars($request['username']); ?> 
                        <form method="POST" action="accept_request.php">
                            <input type="hidden" name="sender_id" value="<?php echo htmlspecialchars($request['sender_id']); ?>">
                            <button type="submit" name="accept_request">Accept</button>
                        </form>
                        <form method="POST" action="decline_request.php">
                            <input type="hidden" name="sender_id" value="<?php echo htmlspecialchars($request['sender_id']); ?>">
                            <button type="submit" name="decline_request">Decline</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No pending friend requests.</li>
            <?php endif; ?>
        </ul>
    </section>

    <section id="badges">
        <h2><?php echo htmlspecialchars($user['username']); ?>'s Badges</h2>
        <ul>
            <li>No badges earned yet.</li>
        </ul>
    </section>
    
    <footer>
        <a href="../index.php">Back to Homepage</a>
    </footer>
</body>
</html>
