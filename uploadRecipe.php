<?php
include('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
$query = "SELECT id FROM app_users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'] ?? null;

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_recipe'])) {
    $recipeName = sanitizeInput($_POST['recipe_name'], $conn);
    $mealType = sanitizeInput($_POST['meal_type'], $conn);
    $ingredients = sanitizeInput($_POST['ingredients'], $conn);
    $instructions = sanitizeInput($_POST['instructions'], $conn);

    if ($user_id) {
        $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, recipe, instructions, meal_type, custom_instructions) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $recipeName, $instructions, $mealType, $ingredients);

        if ($stmt->execute()) {
            $success = "Recipe uploaded and bookmarked successfully!";
        } else {
            $error = "Failed to upload the recipe. Please try again.";
        }
    } else {
        $error = "User not found. Unable to upload the recipe.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Upload Recipe</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Upload Your Recipe</h2>
    <?php if (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="recipe_name">Recipe Name:</label>
        <input type="text" id="recipe_name" name="recipe_name" required>

        <label for="meal_type">Meal Type:</label>
        <select id="meal_type" name="meal_type" required>
            <option value="breakfast">Breakfast</option>
            <option value="lunch">Lunch</option>
            <option value="dinner">Dinner</option>
            <option value="dessert">Dessert</option>
        </select>

        <label for="ingredients">Ingredients (comma-separated):</label>
        <textarea id="ingredients" name="ingredients" rows="5" required></textarea>

        <label for="instructions">Instructions:</label>
        <textarea id="instructions" name="instructions" rows="5" required></textarea>

        <button type="submit" name="upload_recipe">Upload Recipe</button>
    </form>
</div>
</body>
</html>
