<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);

    // Handle image upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = 'uploads/avatar_' . $user_id . '.' . $ext;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filename);

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_pic = ? WHERE id = ?");
        $stmt->bind_param("sssi", $new_username, $new_email, $filename, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
    exit;
}

// Fetch current user info
$stmt = $conn->prepare("SELECT username, email, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $profile_pic);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | Eco Trade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f8e9;
            padding: 40px;
        }

        .form-box {
            background: #ffffff;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1b5e20;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            text-decoration: none;
            color: #2e7d32;
        }

        img.avatar-preview {
            margin-top: 15px;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Edit Profile</h2>
    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <label for="profile_pic">Profile Picture:</label>
        <input type="file" name="profile_pic" accept="image/*">
        <?php if ($profile_pic): ?>
            <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Current Avatar" class="avatar-preview">
        <?php endif; ?>

        <button type="submit">Save Changes</button>
    </form>
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
