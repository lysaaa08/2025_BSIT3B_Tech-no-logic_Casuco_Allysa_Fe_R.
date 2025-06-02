<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$upload_error = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];
    $image_path = null;

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $filename = basename($_FILES['image']['name']);
        $target_file = $upload_dir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        } else {
            $upload_error = "Failed to upload image.";
        }
    }

    if (empty($upload_error)) {
        $stmt = $conn->prepare("INSERT INTO items (user_id, title, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $description, $image_path);
        if ($stmt->execute()) {
            $success_message = "Item posted successfully!";
        } else {
            $upload_error = "Error posting item.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Item | Eco Trade</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f1f8e9;
            font-family: Arial, sans-serif;
        }
        nav {
            background-color: #2e7d32;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .form-container {
            max-width: 500px;
            background: white;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #c8e6c9;
        }
        input, textarea {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            font-size: 1rem;
        }
        input[type="submit"] {
            background-color: #2e7d32;
            color: white;
            cursor: pointer;
            border: none;
        }
        input[type="submit"]:hover {
            background-color: #1b5e20;
        }
        .msg {
            margin-top: 10px;
            font-weight: bold;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
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
    <div>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="index.php">🌿 Browse Items</a>
        <a href="chat.php">💬 Messages</a>
    </div>
    <div>
        <a href="logout.php">🚪 Logout</a>
    </div>
</nav>

<div class="form-container">
    <h2>Post a New Item for Trade</h2>

    <?php if ($success_message): ?>
        <p class="msg success"><?= htmlspecialchars($success_message) ?></p>
    <?php elseif ($upload_error): ?>
        <p class="msg error"><?= htmlspecialchars($upload_error) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Item Title</label>
        <input type="text" name="title" required>

        <label>Description</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Upload Image</label>
        <input type="file" name="image" accept="image/*">

        <input type="submit" value="Post Item">
    </form>
</div>
<footer>
    &copy; 2025 Eco Trade - Reduce, Reuse, Recycle
</footer>
</body>
</html>
