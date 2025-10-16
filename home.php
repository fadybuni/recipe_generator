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
    <style>
        .home-options {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .home-box {
            width: 200px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ff4081;
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            transition: transform 0.3s, background-color 0.3s;
            text-align: center;
        }
        .home-box:hover {
            background-color: #e03570;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
    </header>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <div class="home-options">
        <a href="selectMeal.php" class="home-box">Generate Recipes</a>
        <a href="bookmarks.php" class="home-box">Bookmarked Recipes</a>
        <a href="randomRecipe.php" class="home-box">Generate Random Recipe</a>
        <a href="uploadRecipe.php" class="home-box">Upload Recipe</a>
    </div>
</div>
</body>
</html>
