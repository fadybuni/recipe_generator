<?php
include('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Fetch user_id based on logged-in username
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

// Add bookmark if "bookmark_recipe" is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookmark_recipe'])) {
    $recipe = sanitizeInput($_POST['recipe'], $conn);

    // Check if recipe is already bookmarked
    $checkQuery = $conn->prepare("SELECT * FROM bookmarks WHERE user_id = ? AND recipe = ?");
    $checkQuery->bind_param("is", $user_id, $recipe);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows === 0) {
        // Add new bookmark
        $insertQuery = $conn->prepare("INSERT INTO bookmarks (user_id, recipe) VALUES (?, ?)");
        $insertQuery->bind_param("is", $user_id, $recipe);
        $insertQuery->execute();

        if ($insertQuery->affected_rows > 0) {
            $success = "Recipe bookmarked successfully.";
        } else {
            $error = "Failed to bookmark recipe. Please try again.";
        }
    } else {
        $error = "Recipe is already bookmarked.";
    }
}

// Fetch bookmarks for the logged-in user
$stmt = $conn->prepare("SELECT id, recipe FROM bookmarks WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookmarks = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cooking Chaos - Bookmarked Recipes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1 class="logo">Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Your Bookmarked Recipes</h2>
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <div id="bookmark-list">
        <?php if (!empty($bookmarks)): ?>
            <?php foreach ($bookmarks as $bookmark): ?>
                <div class="bookmark-item">
                    <p><?php echo htmlspecialchars($bookmark['recipe']); ?></p>
                    <form method="POST" action="">
                        <input type="hidden" name="bookmark_id" value="<?php echo $bookmark['id']; ?>">
                        <button type="submit" name="remove_bookmark" class="remove-button">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No bookmarks found. Start bookmarking recipes!</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
