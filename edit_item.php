<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$item_id = $_GET['id'] ?? null;

// Redirect if no ID
if (!$item_id) {
    header("Location: dashboard.php");
    exit;
}

// Fetch the item
$stmt = $conn->prepare("SELECT title, description, image FROM items WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();
$stmt->bind_result($title, $description, $image);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = $_POST['title'];
    $new_description = $_POST['description'];

    // Check if a new image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "uploads/";
        $targetFile = $targetDir . uniqid() . "_" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);

        // Delete old image if exists
        if ($image && file_exists($image)) {
            unlink($image);
        }

        $image = $targetFile;
    }

    // Update the item
    $update = $conn->prepare("UPDATE items SET title = ?, description = ?, image = ? WHERE id = ? AND user_id = ?");
    $update->bind_param("sssii", $new_title, $new_description, $image, $item_id, $user_id);
    $update->execute();
    $update->close();

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item | Eco Trade</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f8e9;
            padding: 40px;
        }
        .edit-form {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2e7d32;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="file"] {
            margin-top: 10px;
        }
        img {
            margin-top: 10px;
            max-width: 100%;
            border-radius: 8px;
        }
        button {
            margin-top: 20px;
            background-color: #2e7d32;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #1b5e20;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2e7d32;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="edit-form">
    <h2>Edit Item</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>

        <label for="image">Current Image:</label>
        <img src="<?= htmlspecialchars($image) ?>" alt="Item Image">

        <label for="image">Replace Image (optional):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Update Item</button>
    </form>

    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
