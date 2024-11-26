<?php
include('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Fetch user_id for the logged-in username
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

//  Remove Bookmark
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_bookmark'])) {
    $bookmarkId = $_POST['bookmark_id'];
    $stmt = $conn->prepare("DELETE FROM bookmarks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $bookmarkId, $user_id);
    $stmt->execute();
}

// Fetch bookmarks for the logged-in user
$stmt = $conn->prepare("SELECT id, recipe, recipe_name FROM bookmarks WHERE user_id = ?");
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
    <div id="bookmark-list">
        <?php if (!empty($bookmarks)): ?>
            <?php foreach ($bookmarks as $bookmark): ?>
                <div class="bookmark-item">
                    <a href="recipeDetails.php?recipe=<?php echo urlencode($bookmark['recipe']); ?>">
                        <?php echo htmlspecialchars($bookmark['recipe_name']); ?>
                    </a>
                    <form method="POST" action="">
                        <input type="hidden" name="bookmark_id" value="<?php echo htmlspecialchars($bookmark['id']); ?>">
                        <button type="submit" name="remove_bookmark">Remove Bookmark</button>
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
