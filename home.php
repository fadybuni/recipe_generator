<?php
include('db.php'); // Database connection

session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1>Cooking Chaos</h1>
    </header>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <div id="home-options">
        <a href="recipeDetails.php" class="home-button">Generate Recipes</a>
        <a href="bookmarks.php" class="home-button">Bookmarked Recipes</a>
    </div>
</div>
</body>
</html>
