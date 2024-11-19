<?php
include('db.php'); // Database connection

session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

// Get the logged-in user's ID
$query = "SELECT id FROM app_users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Fetch all bookmarked recipes for the user
$query = "SELECT id, recipe FROM bookmarks WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookmarks = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bookmarked Recipes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="wrapper">
    <header>
        <h1>Cooking Chaos</h1>
        <a href="home.php" class="home-button">Home</a>
    </header>
    <h2>Your Bookmarked Recipes</h2>
    <?php if (!empty($bookmarks)): ?>
        <ul>
            <?php foreach ($bookmarks as $bookmark): ?>
                <li>
                    <a href="recipeDetails.php?recipe=<?php echo urlencode($bookmark['recipe']); ?>">
                        <?php echo htmlspecialchars($bookmark['recipe']); ?>
                    </a>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="bookmark_id" value="<?php echo $bookmark['id']; ?>">
                        <button type="submit" name="delete_bookmark">Remove</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no bookmarked recipes yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
