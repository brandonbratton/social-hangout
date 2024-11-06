<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Avatar Universe</title>
    <link rel="stylesheet" href="./css/global.css"> <!-- Link to your existing styles -->
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #343a40;
        }

        h2 {
            font-size: 1.75em;
            margin-top: 20px;
            color: #007BFF;
        }

        p {
            line-height: 1.6;
            margin-bottom: 15px;
            color: #495057;
        }

        .contact-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 800px;
            text-align: center; /* Center-align text for better presentation */
        }

        .discord-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #7289DA; /* Discord color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 1.2em;
        }

        .discord-button:hover {
            background-color: #5B6E9D; /* Darker shade on hover */
        }

        footer {
            margin-top: 40px;
            text-align: center;
            color: #868e96;
        }

        footer a {
            color: #007BFF;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="contact-container">
        <h2>Join Our Community</h2>
        <p>If you have any questions, feedback, or just want to chat, feel free to reach out to us. The best way to connect is through our Discord server.</p>
        
        <a href="https://discord.gg/zXWtuVjF" target="_blank" class="discord-button">Join Our Discord</a>
    </div>

    <footer>
        <p>&copy; 2024 Brandon Bratton. All rights reserved. | <a href="terms.php">Terms of Service</a> | <a href="privacy.php">Privacy Policy</a></p>
    </footer>
</body>
</html>
