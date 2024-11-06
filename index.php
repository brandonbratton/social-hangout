<?php
// Start the session
session_start();

// Redirect logged-in users to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: /dashboard/index.php");
    exit();
}

// Include database connection
require './database/info.php';

// Initialize variables
$registrationSuccess = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 10) {
        $error = "Password must be at least 10 characters long!";
    } else {
        try {
            // Check if username or email exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "Username or email already in use!";
            } else {
                // Hash password and register new user
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, coins, gems) VALUES (:username, :email, :password, 50, 10)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->execute();

                $registrationSuccess = true;
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
    <title>Welcome to Avatar Universe</title>
    <link rel="stylesheet" href="./css/global.css">
</head>
<body>
    <header class="site-header">
        <h1>Welcome to Avatar Universe</h1>
        <p>Start creating your own avatar, exploring worlds, and connecting with friends!</p>
    </header>
    
    <section id="features">
        <h2>Explore, Create, and Trade</h2>
        <div class="feature">
            <img src="img/avatar-customization.png" alt="Customize Avatars">
            <p>Design your unique avatar with endless customization options!</p>
        </div>
        <div class="feature">
            <img src="img/marketplace.png" alt="Marketplace">
            <p>Equip your avatar with exclusive items from our marketplace!</p>
        </div>
    </section>

    <!-- Signup Form -->
    <section id="signupForm">
        <h2>Create Your Free Account</h2>
        
        <?php if ($registrationSuccess): ?>
            <p class="success-message">Registration successful! You can now <a href="./login.php">log in</a>.</p>
        <?php else: ?>
            <form method="POST" action="">
                <?php if ($error): ?>
                    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                <input type="password" name="password" placeholder="Password" required>
                
                <button type="submit">Join Now</button>
            </form>
        <?php endif; ?>
        
        <p>Already have an account? <a href="login.php">Log in here</a>.</p>
    </section>
    
    <footer>
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Brandon Bratton. All rights reserved.</p>
        <div class="footer-links">
            <a href="../about.php">About Us</a>
            <a href="../contact.php">Contact</a>
            <a href="../faq.php">FAQ</a>
            <a href="../privacy.php">Terms of Service</a>
            <a href="../terms.php">Privacy Policy</a>
        </div>
    </div>
</footer>

<style>
    footer {
        background-color: #f8f9fa; /* Light background for the footer */
        padding: 20px;
        margin-top: 40px; /* Space above the footer */
        text-align: center;
        border-top: 1px solid #dee2e6; /* Light border on top */
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto; /* Center footer content */
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .footer-links {
        margin-top: 10px;
        display: flex;
        gap: 15px; /* Space between links */
    }

    .footer-links a {
        text-decoration: none;
        color: #007BFF; /* Link color */
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: #0056b3; /* Darker shade on hover */
    }
</style>
</body>
</html>
