<?php
include('db.php'); // Database connection
include('openai.php'); // Include OpenAI logic

session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
$recipes = [];
$instructions = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generate'])) {
        $ingredients = sanitizeInput($_POST['ingredients'], $conn);
        $diet = sanitizeInput($_POST['diet'], $conn);
        $skill = sanitizeInput($_POST['skill'], $conn);

        $recipes = generateRecipes($ingredients, $diet, $skill);
    } elseif (isset($_POST['select_recipe'])) {
        $selectedRecipe = $_POST['recipe'];
        $instructions = getRecipeInstructions($selectedRecipe);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Recipe Generator - Recipes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <form method="POST" action="">
        <label>Select Diet:</label>
        <select name="diet">
            <option value="vegan">Vegan</option>
            <option value="vegetarian">Vegetarian</option>
            <option value="gluten-free">Gluten-Free</option>
            <option value="dairy-free">Dairy-Free</option>
            <option value="keto">Keto</option>
            <option value="halal">Halal</option>
        </select>
        <label>Select Skill Level:</label>
        <select name="skill">
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="expert">Expert</option>
        </select>
        <label>Available Ingredients (comma-separated):</label>
        <input type="text" name="ingredients" required>
        <button type="submit" name="generate">Generate Recipes</button>
    </form>
    <div id="content">
        <h2>Recipes</h2>
        <?php if (!empty($recipes)): ?>
            <form method="POST" action="">
                <?php foreach ($recipes as $recipe): ?>
                    <div>
                        <p><?php echo htmlspecialchars($recipe); ?></p>
                        <button type="submit" name="select_recipe" value="true">Select</button>
                        <input type="hidden" name="recipe" value="<?php echo htmlspecialchars($recipe); ?>">
                    </div>
                <?php endforeach; ?>
            </form>
        <?php elseif (!empty($instructions)): ?>
            <h3>Recipe Instructions</h3>
            <p><?php echo nl2br(htmlspecialchars($instructions)); ?></p>
        <?php else: ?>
            <p>No recipes generated yet. Try submitting the form above!</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
