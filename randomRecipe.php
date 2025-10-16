<?php
include('db.php');
include('openai.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$recipe = $instructions = $ingredients = $cookTime = $mealType = "";
$success = $error = "";

$username = $_SESSION['username'];
$query = "SELECT id FROM app_users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_recipe'])) {
    $cookTime = sanitizeInput($_POST['cook_time'], $conn);
    $mealType = sanitizeInput($_POST['meal_type'], $conn);

    try {
        $recipeData = generateRandomRecipe($cookTime, $mealType);
        $recipe = $recipeData['name'];
        $ingredients = $recipeData['ingredients'];
        $instructions = $recipeData['instructions'];
    } catch (Exception $e) {
        $error = "Failed to generate a random recipe. Please try again.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookmark_recipe'])) {
    $recipeToBookmark = sanitizeInput($_POST['recipe'], $conn);
    $recipeIngredients = sanitizeInput($_POST['ingredients'], $conn);
    $recipeInstructions = sanitizeInput($_POST['instructions'], $conn);
    $mealType = sanitizeInput($_POST['meal_type'], $conn);

    if ($user_id) {
        $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, recipe, instructions, meal_type, custom_instructions) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $recipeToBookmark, $recipeInstructions, $mealType, $recipeIngredients);

        if ($stmt->execute()) {
            $success = "Recipe bookmarked successfully!";
        } else {
            $error = "Failed to bookmark the recipe. It might already be bookmarked.";
        }
    } else {
        $error = "User not found. Unable to bookmark recipe.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Random Recipe Generator</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Generate a Random Recipe</h2>
    <form method="POST" action="">
        <p>Select Meal Type:</p>
        <select name="meal_type" required>
            <option value="breakfast">Breakfast</option>
            <option value="lunch">Lunch</option>
            <option value="dinner">Dinner</option>
            <option value="dessert">Dessert</option>
        </select>
        <p>Select Cooking Time:</p>
        <select name="cook_time" required>
            <option value="15 minutes">15 minutes</option>
            <option value="30 minutes">30 minutes</option>
            <option value="45 minutes">45 minutes</option>
            <option value="1 hour">1 hour</option>
            <option value="2 hours">2 hours</option>
        </select>
        <button type="submit" name="generate_recipe">Generate Recipe</button>
    </form>

    <?php if (!empty($recipe)): ?>
        <h3>Generated Recipe</h3>
        <p><strong>Recipe Name:</strong> <?php echo htmlspecialchars($recipe); ?></p>
        <p><strong>Ingredients:</strong></p>
        <ul>
            <?php foreach (explode("\n", $ingredients) as $ingredient): ?>
                <li><?php echo htmlspecialchars($ingredient); ?></li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Instructions:</strong></p>
        <p><?php echo nl2br(htmlspecialchars($instructions)); ?></p>
        <form method="POST" action="">
            <input type="hidden" name="recipe" value="<?php echo htmlspecialchars($recipe); ?>">
            <input type="hidden" name="ingredients" value="<?php echo htmlspecialchars($ingredients); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($instructions); ?>">
            <input type="hidden" name="meal_type" value="<?php echo htmlspecialchars($mealType); ?>">
            <button type="submit" name="bookmark_recipe">Bookmark Recipe</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
</div>
</body>
</html>
