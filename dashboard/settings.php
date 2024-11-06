<?php
session_start();

// Include database connection
require '../database/info.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch user's details
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize messages
$message = '';
$error = '';
$showAlert = false; // Variable to control the alert display

// Handle form submission for username change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update username
    if (isset($_POST['change_username'])) {
        $newUsername = trim($_POST['new_username']);
        $gemsRequired = 150;

        // Fetch user's current gems
        $gemsStmt = $conn->prepare("SELECT gems FROM users WHERE id = :id");
        $gemsStmt->bindParam(':id', $userId);
        $gemsStmt->execute();
        $currentGems = $gemsStmt->fetchColumn();

        if ($currentGems >= $gemsRequired) {
            // Update username
            $updateUsernameStmt = $conn->prepare("UPDATE users SET username = :username WHERE id = :id");
            $updateUsernameStmt->bindParam(':username', $newUsername);
            $updateUsernameStmt->bindParam(':id', $userId);
            $updateUsernameStmt->execute();

            // Deduct gems
            $updateGemsStmt = $conn->prepare("UPDATE users SET gems = gems - :gems WHERE id = :id");
            $updateGemsStmt->bindParam(':gems', $gemsRequired);
            $updateGemsStmt->bindParam(':id', $userId);
            $updateGemsStmt->execute();

            $message = "Username updated successfully!";
        } else {
            $showAlert = true; // Set to true to show the alert
        }
    }

    // Update email
    if (isset($_POST['change_email'])) {
        $newEmail = trim($_POST['new_email']);
        if (filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $updateEmailStmt = $conn->prepare("UPDATE users SET email = :email WHERE id = :id");
            $updateEmailStmt->bindParam(':email', $newEmail);
            $updateEmailStmt->bindParam(':id', $userId);
            $updateEmailStmt->execute();
            $message = "Email updated successfully!";
        } else {
            $error = "Invalid email format.";
        }
    }

    // Update password
    if (isset($_POST['change_password'])) {
        $newPassword = trim($_POST['new_password']);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updatePasswordStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updatePasswordStmt->bindParam(':password', $hashedPassword);
        $updatePasswordStmt->bindParam(':id', $userId);
        $updatePasswordStmt->execute();
        $message = "Password updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <link rel="stylesheet" href="../css/global.css">
    <style>
        /* Add styles for the alert box */
        .alert {
            display: none; /* Hidden by default */
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #f44336; /* Red */
            color: white;
            border-radius: 5px;
            z-index: 1000; /* Sit on top */
        }
    </style>
    <script>
        // Function to show alert
        function showAlert() {
            document.getElementById('gemAlert').style.display = 'block';
        }

        // Function to hide alert
        function hideAlert() {
            document.getElementById('gemAlert').style.display = 'none';
        }
    </script>
</head>
<body>
    <header>
        <h1>User Settings</h1>
    </header>
     <?php include 'header.php'; ?>
    <main>
        <section>
            <h2>Change Username (100 Gems)</h2>
            <form method="POST" action="">
                <input type="text" name="new_username" placeholder="New Username" required>
                <button type="submit" name="change_username">Change Username</button>
            </form>
        </section>

        <section>
            <h2>Change Email</h2>
            <form method="POST" action="">
                <input type="email" name="new_email" placeholder="New Email" required>
                <button type="submit" name="change_email">Change Email</button>
            </form>
        </section>

        <section>
            <h2>Change Password</h2>
            <form method="POST" action="">
                <input type="password" name="new_password" placeholder="New Password" required>
                <button type="submit" name="change_password">Change Password</button>
            </form>
        </section>

        <?php if ($message): ?>
            <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <!-- Gem alert -->
        <?php if ($showAlert): ?>
            <div id="gemAlert" class="alert">
                You do not have enough gems to change your username.
                <button onclick="hideAlert()">Close</button>
            </div>
            <script>showAlert();</script>
        <?php endif; ?>
    </main>
</body>
</html>
