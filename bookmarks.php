<?php
include('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM app_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$user_id = $user['id'];

=$mealTypes = ['breakfast', 'lunch', 'dinner', 'dessert'];
$bookmarksByMealType = [];

foreach ($mealTypes as $mealType) {
    $stmt = $conn->prepare("SELECT id, recipe FROM bookmarks WHERE user_id = ? AND meal_type = ?");
    $stmt->bind_param("is", $user_id, $mealType);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookmarksByMealType[$mealType] = $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_bookmark'])) {
    $bookmarkId = intval($_POST['bookmark_id']);
    $stmt = $conn->prepare("DELETE FROM bookmarks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $bookmarkId, $user_id);

    if ($stmt->execute()) {
        header("Location: bookmarks.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Bookmarked Recipes</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .meal-section {
            margin-bottom: 20px;
        }
        .meal-section h3 {
            margin-bottom: 10px;
        }
        .bookmark-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .bookmark-item a {
            text-decoration: none;
            color: #ff4081;
            font-weight: bold;
        }
        .bookmark-item a:hover {
            text-decoration: underline;
        }
        .bookmark-item button {
            background-color: #e03570;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .bookmark-item button:hover {
            background-color: #c02c5a;
        }
    </style>
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Your Bookmarked Recipes</h2>
    <?php foreach ($bookmarksByMealType as $mealType => $bookmarks): ?>
        <?php if (!empty($bookmarks)): ?>
            <div class="meal-section">
                <h3><?php echo ucfirst($mealType); ?> Recipes</h3>
                <?php foreach ($bookmarks as $bookmark): ?>
                    <div class="bookmark-item">
                        <a href="recipeDetails.php?recipe=<?php echo urlencode($bookmark['recipe']); ?>&meal_type=<?php echo urlencode($mealType); ?>">
                            <?php echo htmlspecialchars($bookmark['recipe']); ?>
                        </a>
                        <form method="POST" action="">
                            <input type="hidden" name="bookmark_id" value="<?php echo $bookmark['id']; ?>">
                            <button type="submit" name="remove_bookmark">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
</body>
</html>
