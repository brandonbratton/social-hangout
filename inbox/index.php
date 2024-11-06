<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require '../database/info.php';

// Fetch messages for the logged-in user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT m.id, u.username AS sender, m.message, m.created_at 
                         FROM messages m 
                         JOIN users u ON m.sender_id = u.id 
                         WHERE m.recipient_id = :user_id 
                         ORDER BY m.created_at DESC");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch friends of the logged-in user for message sending
$friendsStmt = $conn->prepare("SELECT u.id, u.username 
                                FROM friendships f 
                                JOIN users u ON f.friend_id = u.id 
                                WHERE f.user_id = :user_id");
$friendsStmt->bindParam(':user_id', $userId);
$friendsStmt->execute();
$friends = $friendsStmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $recipientId = intval($_POST['recipient_id']);
    $message = trim($_POST['message']);

    if (empty($message)) {
        $error = "Message cannot be empty!";
    } else {
        try {
            $sendStmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, message) VALUES (:sender_id, :recipient_id, :message)");
            $sendStmt->bindParam(':sender_id', $userId);
            $sendStmt->bindParam(':recipient_id', $recipientId);
            $sendStmt->bindParam(':message', $message);
            $sendStmt->execute();
            $success = "Message sent successfully!";
        } catch (PDOException $e) {
            $error = "Error sending message: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Inbox</title>
    <link rel="stylesheet" href="../css/index.css">
     <link rel="stylesheet" href="../css/inbox.css">
</head>
<body>
    <header>
        <h1>Your Inbox</h1>
    </header>
    <section>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        
        <h2>Send a Message</h2>
        <form method="POST" action="">
            <select name="recipient_id" required>
                <option value="">Select Recipient</option>
                <?php foreach ($friends as $friend): ?>
                    <option value="<?php echo $friend['id']; ?>"><?php echo htmlspecialchars($friend['username']); ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="message" placeholder="Write your message here..." required></textarea>
            <button type="submit" name="send_message">Send Message</button>
        </form>
    </section>

    <section>
        <h2>Your Messages</h2>
        <ul>
            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $message): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($message['sender']); ?></strong>: <?php echo htmlspecialchars($message['message']); ?>
                        <br><em>Sent on <?php echo htmlspecialchars($message['created_at']); ?></em>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No messages found.</li>
            <?php endif; ?>
        </ul>
    </section>
</body>
</html>
