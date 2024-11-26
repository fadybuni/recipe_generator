<?php
include('db.php');
include('openai.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Variables
$recipes = [];
$instructions = "";
$selectedMeal = isset($_GET['meal']) ? $_GET['meal'] : "";
$recipe = isset($_GET['recipe']) ? urldecode($_GET['recipe']) : "";

// Fetch the logged-in user's ID
$query = "SELECT id FROM app_users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'] ?? null;

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate'])) {
        $ingredients = sanitizeInput($_POST['ingredients'], $conn);
        $diet = sanitizeInput($_POST['diet'], $conn);
        $skill = sanitizeInput($_POST['skill'], $conn);

        // Generate recipes using OpenAI
        $recipes = generateRecipes("$selectedMeal, $diet, $ingredients", $diet, $skill);
    } elseif (isset($_POST['select_recipe'])) {
        $recipe = $_POST['recipe'];
        try {
            $instructions = generateRecipeInstructions($recipe);
        } catch (Exception $e) {
            $instructions = "Unable to fetch recipe instructions. Please try again.";
        }
    } elseif (isset($_POST['bookmark_recipe'])) {
        $recipeToBookmark = sanitizeInput($_POST['recipe'], $conn);
        $recipeName = sanitizeInput($_POST['recipe_name'], $conn) ?? 'Unnamed Recipe';
        $recipeInstructions = sanitizeInput($_POST['instructions'], $conn) ?? 'No instructions provided';
        $mealType = sanitizeInput($_POST['meal_type'], $conn) ?? null;

        // Bookmark the recipe
        $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, recipe, recipe_name, instructions, meal_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $recipeToBookmark, $recipeName, $recipeInstructions, $mealType);
        if ($stmt->execute()) {
            $success = "Recipe bookmarked successfully!";
        } else {
            $error = "Failed to bookmark recipe. It might already be bookmarked.";
        }
    }
}

// Handle bookmarked recipe display
if (!empty($recipe) && empty($instructions)) {
    try {
        $instructions = generateRecipeInstructions($recipe);
    } catch (Exception $e) {
        $instructions = "Unable to fetch recipe instructions. Please try again.";
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
        <?php if (!empty($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php elseif (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="recipe" value="<?php echo htmlspecialchars($recipe); ?>">
            <input type="hidden" name="recipe_name" value="<?php echo htmlspecialchars($recipe); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($instructions); ?>">
            <input type="hidden" name="meal_type" value="<?php echo htmlspecialchars($selectedMeal ?? ''); ?>">
            <button type="submit" name="bookmark_recipe">Bookmark Recipe</button>
        </form>
    <?php elseif (!empty($selectedMeal)): ?>
        <h2>Selected Meal: <?php echo ucfirst($selectedMeal); ?></h2>
        <form method="POST" action="">
            <label>Select Diet:</label>
            <select name="diet">
                <option value="n/a">N/A</option>
                <option value="vegan">Vegan</option>
                <option value="vegetarian">Vegetarian</option>
                <option value="pescatarian">Pescatarian</option>
                <option value="keto">Keto</option>
                <option value="paleo">Paleo</option>
                <option value="low-carb">Low Carb</option>
                <option value="gluten-free">Gluten-Free</option>
                <option value="dairy-free">Dairy-Free</option>
                <option value="nut-free">Nut-Free</option>
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
        <?php if (!empty($recipes)): ?>
            <h3>Generated Recipes:</h3>
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <p><?php echo htmlspecialchars($recipe); ?> (Diet: <?php echo htmlspecialchars($_POST['diet']); ?>)</p>
                    <form method="POST" action="">
                        <input type="hidden" name="recipe" value="<?php echo htmlspecialchars($recipe); ?>">
                        <input type="hidden" name="diet" value="<?php echo htmlspecialchars($_POST['diet']); ?>">
                        <button type="submit" name="select_recipe">View Instructions</button>
                        <button type="submit" name="bookmark_recipe">Bookmark Recipe</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: ?>
        <p>No meal or recipe selected. Go back to the previous page.</p>
        <a href="selectMeal.php" class="home-button">Back to Meal Selection</a>
    <?php endif; ?>
</div>
</body>
</html>
