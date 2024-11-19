<?php
$servername = "localhost"; // Your server name
$username = "root";        // Database username (MAMP default)
$password = "root";        // Database password (MAMP default)
$dbname = "my_recipes";    // Your database name

// Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to sanitize input
function sanitizeInput($input, $connection) {
    return mysqli_real_escape_string($connection, htmlspecialchars(trim($input)));
}
?>
