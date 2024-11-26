<?php
include('db.php');
include('openai.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Variables
$recipe = $instructions = $ingredients = $cookTime = $mealType = "";
$success = $error = "";

// Fetch the logged-in user's ID
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
    $recipeInstructions = sanitizeInput($_POST['instructions'], $conn);
    $recipeIngredients = sanitizeInput($_POST['ingredients'], $conn);
    $mealType = sanitizeInput($_POST['meal_type'], $conn);

    if ($user_id) {
        $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, recipe, instructions, meal_type, custom_instructions) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $recipeToBookmark, $recipeInstructions, $mealType, $recipeIngredients);

        if ($stmt->execute()) {
            $success = "Recipe bookmarked successfully!";
        } else {
            $error = "Failed to bookmark recipe. It might already be bookmarked.";
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
    <style>
        /* Styling for the time boxes */
        .time-options {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .time-box {
            width: 120px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid pink;
            border-radius: 8px;
            background-color: white;
            color: pink;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s, filter 0.3s;
        }
        .time-box:hover {
            filter: brightness(85%);
        }
        .time-box.active {
            background-color: pink;
            color: white;
            filter: brightness(75%);
        }

        /* Meal type selection */
        .meal-options {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .meal-option {
            cursor: pointer;
            transition: transform 0.3s, filter 0.3s;
        }
        .meal-option img {
            width: 150px;
            height: 150px;
            border-radius: 12px;
            border: 3px solid pink;
            transition: border-color 0.3s, transform 0.3s, filter 0.3s;
        }
        .meal-option img:hover {
            filter: brightness(85%);
        }
        .meal-option img.selected {
            filter: brightness(75%);
            border-color: white;
            transform: scale(1.1);
        }
        .hidden-input {
            display: none;
        }
    </style>
    <script>
        // JavaScript to toggle active states
        document.addEventListener('DOMContentLoaded', () => {
            // Time selection
            document.querySelectorAll('.time-box').forEach(box => {
                box.addEventListener('click', () => {
                    document.querySelectorAll('.time-box').forEach(b => b.classList.remove('active'));
                    box.classList.add('active');
                    box.querySelector('input').checked = true;
                });
            });

            // Meal type selection
            document.querySelectorAll('.meal-option img').forEach(img => {
                img.addEventListener('click', () => {
                    document.querySelectorAll('.meal-option img').forEach(i => i.classList.remove('selected'));
                    img.classList.add('selected');
                    img.previousElementSibling.checked = true;
                });
            });
        });
    </script>
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Generate a Random Recipe</h2>
    <form method="POST" action="">
        <!-- Time Selection -->
        <p>Select Cooking Time:</p>
        <div class="time-options">
            <label class="time-box">
                <input type="radio" name="cook_time" value="15 minutes" class="hidden-input" required> 15 minutes
            </label>
            <label class="time-box">
                <input type="radio" name="cook_time" value="30 minutes" class="hidden-input" required> 30 minutes
            </label>
            <label class="time-box">
                <input type="radio" name="cook_time" value="45 minutes" class="hidden-input" required> 45 minutes
            </label>
            <label class="time-box">
                <input type="radio" name="cook_time" value="1 hour" class="hidden-input" required> 1 hour
            </label>
            <label class="time-box">
                <input type="radio" name="cook_time" value="2 hours" class="hidden-input" required> 2 hours
            </label>
        </div>

        <!-- Meal Type Selection -->
        <p>Select Meal Type:</p>
        <div class="meal-options">
            <label class="meal-option">
                <input type="radio" name="meal_type" value="breakfast" class="hidden-input" required>
                <img src="images/breakfast.jpg" alt="Breakfast">
            </label>
            <label class="meal-option">
                <input type="radio" name="meal_type" value="lunch" class="hidden-input" required>
                <img src="images/lunch.jpg" alt="Lunch">
            </label>
            <label class="meal-option">
                <input type="radio" name="meal_type" value="dinner" class="hidden-input" required>
                <img src="images/dinner.jpg" alt="Dinner">
            </label>
            <label class="meal-option">
                <input type="radio" name="meal_type" value="dessert" class="hidden-input" required>
                <img src="images/dessert.jpg" alt="Dessert">
            </label>
        </div>

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
