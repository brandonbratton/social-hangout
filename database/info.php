<?php
// Database configuration
$host = 'localhost'; // or the server address
$dbname = 'avataruniverse';
$username = 'brandon';
$password = '#Buffalo716';

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO to throw exceptions for error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle the error if connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>
