<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('db.php');

if (isset($_POST['register'])) {
    $username = sanitizeInput($_POST['username'], $conn);
    $password = md5($_POST['password']); // Replace with password_hash() in production

    $query = "INSERT INTO app_users (username, password) VALUES ('$username', '$password')";

    if (mysqli_query($conn, $query)) {
        echo "<p style='color:green;'>Registration successful! Please login.</p>";
    } else {
        echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
    }
}

if (isset($_POST['login'])) {
    $username = sanitizeInput($_POST['username'], $conn);
    $password = md5($_POST['password']); // Replace with password_hash() in production

    $query = "SELECT * FROM app_users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        session_start();
        $_SESSION['username'] = $username;
        header('Location: recipeDetails.php');
    } else {
        echo "<p style='color:red;'>Invalid username or password.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Recipe Generator - Login/Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <h1>Recipe Generator</h1>
    <form method="POST" action="">
        <h2>Login</h2>
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <form method="POST" action="">
        <h2>Register</h2>
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="register">Register</button>
    </form>
</div>
</body>
</html>
