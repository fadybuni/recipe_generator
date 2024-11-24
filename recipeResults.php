<?php
include('db.php');
include('openai.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$recipes = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate'])) {
    $meal = sanitizeInput($_POST['meal'], $conn);
    $diet = sanitizeInput($_POST['diet'], $conn);
    $skill = sanitizeInput($_POST['skill'], $conn);
    $ingredients = sanitizeInput($_POST['ingredients'], $conn);

    $recipes = generateRecipes("$meal, $diet, $ingredients", $diet, $skill);
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
        <h1 class="logo">Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Generated Recipes</h2>
    <div id="recipe-grid">
        <?php if (!empty($recipes)): ?>
            <?php foreach ($recipes as $recipe): ?>
                <?php
                $recipeName = htmlspecialchars(trim($recipe));
                if ($recipeName): ?>
                    <div class="recipe-box">
                        <h3><?php echo $recipeName; ?></h3>
                        <a href="recipeDetails.php?recipe=<?php echo urlencode($recipeName); ?>" class="view-recipe-button">View Recipe</a>
                        <form method="POST" action="bookmarks.php">
                            <input type="hidden" name="recipe" value="<?php echo $recipeName; ?>">
                            <button type="submit" name="bookmark_recipe">Bookmark</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recipes generated. Try again!</p>
            <a href="recipeDetails.php?meal=<?php echo htmlspecialchars($meal); ?>" class="home-button">Back to Input</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
