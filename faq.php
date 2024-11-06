<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequently Asked Questions - Avatar Universe</title>
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

        .faq-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 800px;
        }

        .faq-item {
            margin-bottom: 15px;
        }

        .faq-item h2 {
            font-size: 1.5em;
            color: #007BFF;
            cursor: pointer;
        }

        .faq-item p {
            display: none; /* Hidden by default */
            margin-top: 5px;
            line-height: 1.6;
            color: #495057;
        }

        .faq-item.active p {
            display: block; /* Show when active */
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
    <script>
        // Function to toggle FAQ answers
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item h2');

            faqItems.forEach(item => {
                item.addEventListener('click', function() {
                    const faqItem = this.parentElement;
                    faqItem.classList.toggle('active');
                });
            });
        });
    </script>
</head>
<body>
    <header>
        <h1>Frequently Asked Questions</h1>
    </header>

    <div class="faq-container">
        <div class="faq-item">
            <h2>What is Avatar Universe?</h2>
            <p>Avatar Universe is a 2D social platform where users can create customizable avatars, chat with others, and participate in various activities.</p>
        </div>

        <div class="faq-item">
            <h2>How do I create an account?</h2>
            <p>You can create an account by clicking on the "Sign Up" button on the homepage and filling out the required information.</p>
        </div>

        <div class="faq-item">
            <h2>How do I purchase items?</h2>
            <p>You can purchase items from the marketplace. Simply select the item you wish to buy and follow the prompts to complete your purchase.</p>
        </div>

        <div class="faq-item">
            <h2>Can I change my avatar?</h2>
            <p>Yes, you can customize your avatar anytime from your profile settings.</p>
        </div>

        <div class="faq-item">
            <h2>How do I contact support?</h2>
            <p>If you need assistance, please visit our <a href="contact.php">Contact Us</a> page to reach our support team.</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Brandon Bratton. All rights reserved. | <a href="privacy.php">Terms of Service</a> | <a href="contact.php">Contact Us</a></p>
    </footer>
</body>
</html>
