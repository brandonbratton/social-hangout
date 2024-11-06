<?php
session_start();

// Include database connection
require '../database/info.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all users
$usersStmt = $conn->prepare("SELECT id, username, last_activity FROM users");
$usersStmt->execute();
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Define online status
$currentTime = new DateTime();
$onlineThreshold = new DateTime('-5 minutes'); // Define "online" as being active within the last 5 minutes

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Players</title>
    <link rel="stylesheet" href="../css/global.css">
</head>

<?php include '../dashboard/header.php'; ?>


<body>
    <header>
        <h1>All Players</h1>
    </header>
    
    <section>
        <h2>Player List</h2>
        <ul>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <li>
                        <strong>
                            <a href="../users/profile.php?id=<?php echo $user['id']; ?>" class="username-link">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </a>
                        </strong> - 
                        <?php 
                        // Check if last_activity is not null and compare
                        if (!is_null($user['last_activity'])) {
                            $lastActivity = new DateTime($user['last_activity']);
                            echo ($lastActivity >= $onlineThreshold) ? '<span class="online">Online</span>' : '<span class="offline">Offline</span>';
                        } else {
                            echo '<span class="offline">Offline</span>';
                        }
                        ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No players found.</li>
            <?php endif; ?>
        </ul>
    </section>

    <footer>
        <a href="../index.php">Back to Homepage</a>
    </footer>
</body>
</html>

