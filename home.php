<?php
include('db.php');
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
        <h1 class="logo">Cooking Chaos</h1>
    </header>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <div id="home-options">
        <!-- Generate Recipes Option -->
        <a href="selectMeal.php" class="home-button large-button">Generate Recipes</a>
        
        <!-- View Bookmarked Recipes Option -->
        <a href="bookmarks.php" class="home-button large-button">Bookmarked Recipes</a>
        
        <!-- New Generate Random Recipe Button -->
        <a href="randomRecipe.php" class="home-button large-button">Generate Random Recipe</a>
    </div>
</div>
</body>
</html>
