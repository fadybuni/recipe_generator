<?php
include('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Select Meal</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Select a Meal Type</h2>
    <div id="meal-options">
        <a href="recipeDetails.php?meal=breakfast">
            <img src="images/breakfast.jpg" alt="Breakfast" class="meal-image">
            <p>Breakfast</p>
        </a>
        <a href="recipeDetails.php?meal=lunch">
            <img src="images/lunch.jpg" alt="Lunch" class="meal-image">
            <p>Lunch</p>
        </a>
        <a href="recipeDetails.php?meal=dinner">
            <img src="images/dinner.jpg" alt="Dinner" class="meal-image">
            <p>Dinner</p>
        </a>
        <a href="recipeDetails.php?meal=dessert">
            <img src="images/dessert.jpg" alt="Dessert" class="meal-image">
            <p>Dessert</p>
        </a>
    </div>
</div>
</body>
</html>
