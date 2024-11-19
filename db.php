<?php
$servername = "localhost"; // Replace with your server name if different
$username = "root";        // Replace with your database username
$password = "root";        // Replace with your database password
$dbname = "my_recipes";    // Ensure this matches your database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Input sanitization function
function sanitizeInput($input, $connection) {
    return mysqli_real_escape_string($connection, htmlspecialchars(trim($input)));
}
?>
