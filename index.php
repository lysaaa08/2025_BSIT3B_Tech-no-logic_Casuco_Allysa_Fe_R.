<?php
session_start();
require_once 'includes/db.php';

// Get current user ID if logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Search filter
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchResults = [];

if ($search_term !== '') {
    $search_term_wildcard = '%' . $search_term . '%';

    // Search other users' items
    $stmt = $conn->prepare("SELECT items.*, users.username FROM items 
                            JOIN users ON items.user_id = users.id 
                            WHERE items.user_id != ? AND (items.title LIKE ? OR items.description LIKE ?) 
                            ORDER BY items.created_at DESC");
    $stmt->bind_param("iss", $user_id, $search_term_wildcard, $search_term_wildcard);
    $stmt->execute();
    $result = $stmt->get_result();
    $searchResults = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // No search: fetch all items (excluding own if logged in)
    $stmt = $conn->prepare("SELECT items.*, users.username FROM items 
                            JOIN users ON items.user_id = users.id 
                            WHERE items.user_id != ? 
                            ORDER BY items.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $searchResults = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Eco Trade - Recycle and Trade</title>
    <link rel="stylesheet" href="assets/stye.css">
    <style>
        .item-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .item-card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 15px;
            width: 250px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .item-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
        }
        
    </style>
</head>
<body>

<header style="position: relative; background: url('assets/images/bg1.jpg') no-repeat center center/cover; color: white;">
    <!-- LOGO positioned absolutely in top-left corner -->
    <img src="assets/images/ecoTrade_logo.png" alt="Eco Trade Logo" style="position: absolute; top: 20px; left: 20px; height: 200px; z-index: 10;">

    <div style="background-color: rgba(0, 0, 0, 0.5); padding: 60px 20px; text-align: center;">
        <h1>Eco Trade</h1>
        <p>Trade your unused items and recycle with the community</p>
        <div class="auth-buttons" style="margin-top: 10px;">
            <a href="register.php" style="color: white; background: #2e7d32; padding: 8px 12px; border-radius: 5px; margin-right: 10px;">Sign Up</a>
            <a href="login.php" style="color: white; background: #2e7d32; padding: 8px 12px; border-radius: 5px;">Login</a>
        </div>
    </div>
</header>




<main>
    <form method="GET" action="index.php" style="margin-bottom: 20px; text-align: center;">
        <input type="text" name="search" placeholder="Search for items..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
        <button type="submit" style="padding: 10px 15px; background-color: #2e7d32; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
    </form>

    <div class="carousel-container">
        <img src="assets/images/bike.jpg" alt="Bike">
        <img src="assets/images/books.jpg" alt="Books">
        <img src="assets/images/cellphone.jpg" alt="Electronics">
        <img src="assets/images/plastic.png" alt="Plastic Items">
        <img src="assets/images/furniture.jpg" alt="Furniture">
    </div>

    <?php if (!empty($searchResults)): ?>
        <div class="all-items">
            <h2><?= $search_term ? 'Search Results' : 'Latest Uploaded Items' ?></h2>
            <div class="item-grid">
                <?php foreach ($searchResults as $item): ?>
                    <div class="item-card">
                        <?php if (!empty($item['image'])): ?>
                           <img src="<?= htmlspecialchars($item['image']) ?>" alt="Item Image">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p><strong>Posted by:</strong> <?= htmlspecialchars($item['username']) ?></p>
                        <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <p class="search-results" style="text-align: center;">No items found<?= $search_term ? ' for "<strong>' . htmlspecialchars($search_term) . '</strong>"' : '' ?>.</p>
    <?php endif; ?>
</main>


<footer>
    &copy; 2025 Eco Trade - Reduce, Reuse, Recycle
</footer>

<script>
    function handlePostClick() {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert("You must log in first to post an item.");
            window.location.href = "login.php";
        <?php else: ?>
            window.location.href = "post_item.php";
        <?php endif; ?>
    }
</script>

</body>
</html> 