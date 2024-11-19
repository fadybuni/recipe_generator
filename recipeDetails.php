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
$selectedMeal = isset($_GET['meal']) ? $_GET['meal'] : "";

// Get the logged-in user's ID
$query = "SELECT id FROM app_users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generate'])) {
        $ingredients = sanitizeInput($_POST['ingredients'], $conn);
        $diet = sanitizeInput($_POST['diet'], $conn);
        $skill = sanitizeInput($_POST['skill'], $conn);

        $recipes = generateRecipes("$selectedMeal, $diet, $ingredients", $diet, $skill);
    } elseif (isset($_POST['select_recipe'])) {
        $selectedRecipe = $_POST['recipe'];
        $instructions = getRecipeInstructions($selectedRecipe); // Simulates fetching instructions
    } elseif (isset($_POST['bookmark_recipe'])) {
        $recipeToBookmark = $_POST['recipe'];
        $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, recipe) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $recipeToBookmark);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Recipes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1>Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

    <h2>Select a Meal Type</h2>
    <div id="meal-options">
        <a href="?meal=breakfast">
            <img src="images/breakfast.jpg" alt="Breakfast" class="meal-image">
            <p>Breakfast</p>
        </a>
        <a href="?meal=lunch">
            <img src="images/lunch.jpg" alt="Lunch" class="meal-image">
            <p>Lunch</p>
        </a>
        <a href="?meal=dinner">
            <img src="images/dinner.jpg" alt="Dinner" class="meal-image">
            <p>Dinner</p>
        </a>
        <a href="?meal=dessert">
            <img src="images/dessert.jpg" alt="Dessert" class="meal-image">
            <p>Dessert</p>
        </a>
    </div>

    <?php if (!empty($selectedMeal)): ?>
        <h2>Selected Meal: <?php echo ucfirst($selectedMeal); ?></h2>
        <form method="POST" action="">
            <label>Select Diet:</label>
            <select name="diet">
                <option value="n/a">N/A</option>
                <option value="vegan">Vegan</option>
                <option value="vegetarian">Vegetarian</option>
                <option value="gluten-free">Gluten-Free</option>
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
    <?php endif; ?>

    <div id="content">
        <h2>Recipes</h2>
        <?php if (!empty($recipes)): ?>
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <p><?php echo htmlspecialchars($recipe); ?></p>
                    <form method="POST" action="">
                        <input type="hidden" name="recipe" value="<?php echo htmlspecialchars($recipe); ?>">
                        <button type="submit" name="select_recipe">View Instructions</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($instructions)): ?>
            <h3>Recipe Instructions</h3>
            <div id="instructions">
                <p><strong>Recipe:</strong> <?php echo htmlspecialchars($selectedRecipe); ?></p>
                <p><?php echo nl2br(htmlspecialchars($instructions)); ?></p>
                <form method="POST" action="">
                    <input type="hidden" name="recipe" value="<?php echo htmlspecialchars($selectedRecipe); ?>">
                    <button type="submit" name="bookmark_recipe">Bookmark Recipe</button>
                </form>
            </div>
        <?php else: ?>
            <p>No recipes generated yet. Select a meal type to start!</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
