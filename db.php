<?php
// Database connection
$servername = 'localhost'; // Replace with your database server name if different
$username = 'root';        // Replace with your database username
$password = 'root';            // Replace with your database password
$dbname = 'my_recipes';    // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize input
function sanitizeInput($input, $conn) {
    return mysqli_real_escape_string($conn, trim($input));
}
