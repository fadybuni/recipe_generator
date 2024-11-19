<?php
include('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $diet = sanitizeInput($_POST['diet'], $conn);
    $skill = sanitizeInput($_POST['skill'], $conn);
    $ingredients = sanitizeInput($_POST['ingredients'], $conn);

    $ingredientList = implode("', '", explode(',', $ingredients));
    $query = "SELECT * FROM app_recipes WHERE diet = '$diet' AND skill_level = '$skill' AND ingredients LIKE '%$ingredientList%'";
    $result = mysqli_query($conn, $query);
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
            <option value="non-veg">Non-Vegetarian</option>
        </select>
        <label>Select Skill Level:</label>
        <select name="skill">
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="expert">Expert</option>
        </select>
        <label>Available Ingredients (comma-separated):</label>
        <input type="text" name="ingredients" required>
        <button type="submit">Generate Recipes</button>
    </form>
    <div id="content">
        <h2>Recipes</h2>
        <?php if (!empty($result) && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="recipeImg">
                    <h3><?php echo htmlspecialchars($row['recipe_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No recipes found based on your preferences.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
