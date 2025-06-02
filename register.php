<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Avatar upload handling
    $avatarPath = 'assets/default_avatar.png'; // Default avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['avatar']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExt)) {
            $newName = uniqid('avatar_', true) . '.' . $fileExt;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                $avatarPath = $targetPath;
            } else {
                $error = "Failed to upload avatar.";
            }
        } else {
            $error = "Invalid avatar file type.";
        }
    }

    if (!$error && $username && $email && $password && $confirm) {
        if ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            // Check for existing email or username
            $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $check->bind_param("ss", $email, $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Email or username already in use.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_pic) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $hashed, $avatarPath);

                if ($stmt->execute()) {
                    $success = "Account created! Redirecting to login...";
                    header("refresh:2; url=login.php");
                } else {
                    $error = "Something went wrong. Please try again.";
                }

                $stmt->close();
            }

            $check->close();
        }
    } elseif (!$error) {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | Eco Trade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .signup-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #2e7d32;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #1b5e20;
        }

        .link {
            margin-top: 15px;
            text-align: center;
        }

        .link a {
            color: #388e3c;
            text-decoration: none;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="signup-box">
    <h2>Sign Up</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Username" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="password" name="confirm" placeholder="Confirm Password" required />
        <label for="avatar">Upload Avatar:</label>
        <input type="file" name="avatar" accept="image/*">
        <button type="submit" name="register">Register</button>
    </form>

    <div class="link">
        Already have an account? <a href="login.php">Log in</a>
    </div>
</div>

</body>
</html>
