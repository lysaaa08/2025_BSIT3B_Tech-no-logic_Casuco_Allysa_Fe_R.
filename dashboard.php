<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];

// Get user info for profile section
$user_stmt = $conn->prepare("SELECT username, email, profile_pic FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($username, $email, $avatar);
$user_stmt->fetch();
$user_stmt->close();

// Unread messages count
$notifStmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$notifStmt->bind_param("i", $user_id);
$notifStmt->execute();
$notifStmt->bind_result($unread_count);
$notifStmt->fetch();
$notifStmt->close();

// Search filter
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// User's uploaded items
$user_items_stmt = $conn->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
$user_items_stmt->bind_param("i", $user_id);
$user_items_stmt->execute();
$user_items_result = $user_items_stmt->get_result();
$user_items = $user_items_result->fetch_all(MYSQLI_ASSOC);
$user_items_stmt->close();

// Other users' items with search
$search_term = $_GET['search'] ?? '';

if ($search_term) {
    $search_term_wildcard = '%' . $search_term . '%';
    $other_items_stmt = $conn->prepare("SELECT items.*, users.username FROM items JOIN users ON items.user_id = users.id WHERE items.user_id != ? AND (items.title LIKE ? OR items.description LIKE ?) ORDER BY items.created_at DESC");
    $other_items_stmt->bind_param("iss", $user_id, $search_term_wildcard, $search_term_wildcard);
} else {
    $other_items_stmt = $conn->prepare("SELECT items.*, users.username FROM items JOIN users ON items.user_id = users.id WHERE items.user_id != ? ORDER BY items.created_at DESC");
    $other_items_stmt->bind_param("i", $user_id);
}

$other_items_stmt->execute();
$other_items_result = $other_items_stmt->get_result();
$other_items = $other_items_result->fetch_all(MYSQLI_ASSOC);
$other_items_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Eco Trade</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f8e9;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #2e7d32;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            margin-right: 15px;
        }
        nav a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .section {
            margin-bottom: 40px;
        }

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

        .item-card h4 {
            margin: 10px 0 5px;
            color: #1b5e20;
        }

        .item-card p {
            margin: 0;
            color: #555;
        }

        .item-card a {
            display: inline-block;
            margin-top: 10px;
            background-color: #2e7d32;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .item-card a:hover {
            background-color: #1b5e20;
        }

        .profile-box {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .profile-box img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }

        .search-bar {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 300px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .search-bar button {
            padding: 10px 15px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            margin-left: 10px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #1b5e20;
        }

        footer {
    margin-top: 60px;
    padding: 20px;
    background-color: #a5d6a7;
    text-align: center;
    color: #2e7d32;
}
    </style>
</head>
<body>

<!-- NAVIGATION BAR -->
<nav>
    <div style="display: flex; align-items: center; gap: 15px;">
        <img src="assets/images/ecoTrade_logo.png" alt="Eco Trade Logo" style="height: 40px;">
        <a href="dashboard.php">🌿 Browse Items</a>
        <a href="post_item.php">➕ Post Item</a>
        <a href="message.php">Messages <?= $unread_count > 0 ? "<strong>($unread_count)</strong>" : "" ?></a>
    </div>
    <div>
        <a href="logout.php">🚪 Logout</a>
    </div>
</nav>


<!-- MAIN CONTENT -->
<div class="container">

    <!-- Profile Section -->
    <div class="profile-box">
        <img src="<?= htmlspecialchars($avatar ?: 'assets/default_avatar.png') ?>" alt="Profile Picture">
        <div>
    <h3><?= htmlspecialchars($username) ?></h3>
    <p><?= htmlspecialchars($email) ?></p>
    <a href="edit_profile.php" style="display:inline-block; margin-top:10px; background:#2e7d32; color:#fff; padding:6px 12px; border-radius:5px; text-decoration:none;">Edit Profile</a>
</div>

    </div>

    <!-- Your Items Section -->
    <div class="section">
        <h2>Your Uploaded Items</h2>
        <div class="item-grid">
            <?php if (!empty($user_items)): ?>
                <?php foreach ($user_items as $item): ?>
                   <div class="item-card">
    <img src="<?= htmlspecialchars($item['image']) ?>" alt="Your Item">
    <h4><?= htmlspecialchars($item['title']) ?></h4>
    <p><?= htmlspecialchars($item['description']) ?></p>

    <div style="margin-top:10px;">
        <a href="edit_item.php?id=<?= $item['id'] ?>" style="margin-right:10px; background:#0277bd; color:white; padding:6px 12px; border-radius:5px; text-decoration:none;">✏️ Edit</a>

        <form action="delete_item.php" method="POST" style="display:inline;">
            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
            <button type="submit" onclick="return confirm('Are you sure you want to delete this item?')" style="background:#c62828; color:white; padding:6px 12px; border:none; border-radius:5px; cursor:pointer;">🗑️ Delete</button>
        </form>
    </div>
</div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven't uploaded any items yet. <a href="post_item.php">Post one now!</a></p>
            <?php endif; ?>
        </div>
    </div>


    <!-- Browse Other Items Section -->
    <div class="section">
        <!-- Search Bar -->
<form method="GET" action="dashboard.php" style="margin-bottom: 20px; text-align: center;">
    <input type="text" name="search" placeholder="Search for items..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
    <button type="submit" style="padding: 10px 15px; background-color: #2e7d32; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
</form>

        <h2>Browse Items to Trade</h2>
        <div class="item-grid">
            <?php if (!empty($other_items)): ?>
                <?php foreach ($other_items as $item): ?>
                    <div class="item-card">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="Item Image">
                        <h4><?= htmlspecialchars($item['title']) ?></h4>
                        <p>Posted by: <?= htmlspecialchars($item['username']) ?></p>
                        <a href="item_details.php?id=<?= $item['id'] ?>">View & Message</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>
    <?= $search_term ? 'No items match your search.' : 'No items from other users yet.' ?>
</p>

            <?php endif; ?>
        </div>
    </div>

</div>

<footer>
    &copy; 2025 Eco Trade - Reduce, Reuse, Recycle
</footer>

</body>
</html>
