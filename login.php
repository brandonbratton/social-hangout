<?php
// Include database connection
require './database/info.php';

// Initialize variables to hold error messages and success status
$loginSuccess = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Both fields are required!";
    } else {
        try {
            // Prepare and execute the query to find the user
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify user exists and check the password
            if ($user && password_verify($password, $user['password'])) {
                // Start session and save user data
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $loginSuccess = true;
                
                // Redirect to dashboard after successful login
                header("Location: ./dashboard/index.php");
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } catch (PDOException $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Avatar Universe</title>
    <link rel="stylesheet" href="./css/global.css">
</head>
<body>
    <header>
        <h1>Login to Adventure World!</h1>
        <p>Welcome back! Please log in to continue your adventure.</p>
    </header>
    
    <!-- Login Form -->
    <section id="loginForm">
        <h2>Log In</h2>

        <?php if ($loginSuccess): ?>
            <p class="success-message">Login successful! Redirecting to your dashboard...</p>
        <?php else: ?>
            <form method="POST" action="">
                <?php if ($error): ?>
                    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                <input type="password" name="password" placeholder="Password" required>
                
                <button type="submit">Log In</button>
            </form>
        <?php endif; ?>
        
        <p>Don't have an account? <a href="../index.php">Sign up here</a>.</p>
    </section>
</body>
</html>
