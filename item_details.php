<?php
session_start();
require_once 'includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Item not found.";
    exit;
}

$item_id = intval($_GET['id']);

// Fetch item details
$stmt = $conn->prepare("SELECT items.*, users.username, users.id AS owner_id FROM items JOIN users ON items.user_id = users.id WHERE items.id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Item not found.";
    exit;
}

$item = $result->fetch_assoc();
$logged_in_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($item['title']) ?> | Eco Trade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        h2 {
            color: #2e7d32;
            margin-top: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
        }

        .message-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #388e3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
        }

        .message-btn:hover {
            background-color: #2e7d32;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <img src="<?= htmlspecialchars($item['image']) ?>" alt="Item Image">

    <h2><?= htmlspecialchars($item['title']) ?></h2>
    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($item['description'])) ?></p>
    <p><strong>Uploaded by:</strong> <?= htmlspecialchars($item['username']) ?></p>

    <?php if ($logged_in_user && $logged_in_user !== $item['owner_id']): ?>
        <a href="chat.php?user_id=<?= $item['owner_id'] ?>" class="message-btn">Message Owner</a>
    <?php elseif (!$logged_in_user): ?>
        <a href="login.php" class="message-btn">Login to Message</a>
    <?php endif; ?>

    <div class="back-link">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
