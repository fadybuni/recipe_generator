<?php
$servername = 'localhost';
$username = 'root';        
$password = 'root'; 
$dbname = 'my_recipes';    

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitizeInput($input, $conn) {
    if ($input === null) {
        return '';
    }
    return mysqli_real_escape_string($conn, trim($input));
}
