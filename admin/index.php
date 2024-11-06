<?php
// Start the session
session_start();

// Include database connection
require '../database/info.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch user's details including their role
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user is an admin or moderator
if (!$user || !in_array($user['role'], ['admin', 'moderator'])) {
    echo "You do not have permission to access this page.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/global.css"> <!-- Link your CSS file -->
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <p>Your Role: <?php echo htmlspecialchars($user['role']); ?></p>
        <nav>
            <ul>
                <li><a href="view_users.php">View Users</a></li>
                <li><a href="manage_content.php">Manage Content</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <li><a href="../dashboard/logout.php">Log Out</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Admin Dashboard</h2>
        <p>Here you can manage users, content, and reports.</p>
        <!-- Additional admin functionalities can go here -->
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Avatar World</p>
    </footer>
</body>
</html>
