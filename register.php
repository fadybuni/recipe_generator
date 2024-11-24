<?php
include('db.php');
session_start();

if (isset($_SESSION['username'])) {
    header('Location: home.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'], $conn);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username already exists
        $checkQuery = $conn->prepare("SELECT * FROM app_users WHERE username = ?");
        $checkQuery->bind_param("s", $username);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows > 0) {
            $error = "Username is already taken.";
        } else {
            // Hash the password and insert new user
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insertQuery = $conn->prepare("INSERT INTO app_users (username, password) VALUES (?, ?)");
            $insertQuery->bind_param("ss", $username, $hashed_password);
            $insertQuery->execute();

            if ($insertQuery->affected_rows > 0) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Failed to register. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
    </header>
    <h2>Register</h2>
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
        <a href="index.php" class="home-button">Go to Login</a>
    <?php else: ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="index.php">Login here</a>.</p>
    <?php endif; ?>
</div>
</body>
</html>
