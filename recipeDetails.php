<?php
include('db.php');
include('openai.php'); // Include OpenAI API logic
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Check for meal selection or recipe details
$meal = isset($_GET['meal']) ? $_GET['meal'] : "";
$recipe = isset($_GET['recipe']) ? $_GET['recipe'] : "";
$instructions = "";

if (!empty($recipe)) {
    try {
        // Fetch detailed instructions for the selected recipe
        $instructions = generateRecipeInstructions($recipe);
    } catch (Exception $e) {
        $instructions = "Failed to fetch instructions. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Recipe Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <?php if (!empty($recipe)): ?>
        <h2><?php echo htmlspecialchars($recipe); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($instructions)); ?></p>
        <form method="POST" action="bookmarks.php">
            <input type="hidden" name="recipe" value="<?php echo htmlspecialchars($recipe); ?>">
            <button type="submit" name="bookmark_recipe">Bookmark Recipe</button>
        </form>
    <?php elseif (!empty($meal)): ?>
        <h2>Selected Meal: <?php echo htmlspecialchars(ucfirst($meal)); ?></h2>
        <form method="POST" action="recipeResults.php">
            <input type="hidden" name="meal" value="<?php echo htmlspecialchars($meal); ?>">
            <label for="diet">Select Diet:</label>
            <select name="diet" id="diet">
                <option value="n/a">N/A</option>
                <option value="vegan">Vegan</option>
                <option value="vegetarian">Vegetarian</option>
                <option value="gluten-free">Gluten-Free</option>
                <option value="dairy-free">Dairy-Free</option>
                <option value="keto">Keto</option>
                <option value="halal">Halal</option>
                <option value="paleo">Paleo</option>
                <option value="low-carb">Low-Carb</option>
            </select>
            <label for="skill">Select Skill Level:</label>
            <select name="skill" id="skill">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="expert">Expert</option>
            </select>
            <label for="ingredients">Available Ingredients (comma-separated):</label>
            <input type="text" name="ingredients" id="ingredients" required>
            <button type="submit" name="generate">Generate Recipes</button>
        </form>
    <?php else: ?>
        <p>No meal or recipe selected. Go back to the previous page.</p>
        <a href="selectMeal.php" class="home-button">Back to Meal Selection</a>
    <?php endif; ?>
</div>
</body>
</html>
